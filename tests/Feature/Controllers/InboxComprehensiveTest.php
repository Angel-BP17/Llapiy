<?php

namespace Tests\Feature\Controllers;

use App\Models\Andamio;
use App\Models\Block;
use App\Models\Box;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InboxComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'inbox.view', 'guard_name' => 'web']);
        $adminRole->givePermissionTo('inbox.view');

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    /**
     * 1. Etapa de Contrato - BANDEJA
     */
    public function test_inbox_index_returns_strict_contract()
    {
        Block::factory()->count(2)->create(['box_id' => null]);

        $response = $this->actingAs($this->adminUser)->get('/bandeja');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('inbox/index')
                ->has('documents')
                ->has('areas')
                ->has('sections')
                ->has('maxUploadSize')
                ->has('maxUploadSizeFormatted')
            );
    }

    /**
     * 2. Etapa de Validación - ASIGNAR UBICACIÓN
     */
    public function test_inbox_update_storage_requires_valid_data()
    {
        $block = Block::factory()->create(['box_id' => null]);

        $response = $this->actingAs($this->adminUser)->put("/inbox/update-storage/{$block->id}", [
            'box_id' => 99999, // Inexistente
        ]);

        $response->assertSessionHasErrors(['n_box']);
    }

    /**
     * 3. Lógica de Negocio - FILTROS
     */
    public function test_inbox_only_shows_blocks_without_box()
    {
        $section = Section::factory()->create();
        $andamio = Andamio::factory()->create(['section_id' => $section->id]);
        $box = Box::factory()->create(['andamio_id' => $andamio->id]);

        Block::factory()->create(['box_id' => null, 'asunto' => 'Pendiente']);
        Block::factory()->create(['box_id' => $box->id, 'asunto' => 'Archivado']);

        $response = $this->actingAs($this->adminUser)->get('/bandeja');

        $response->assertInertia(fn (Assert $page) => $page
            ->has('documents', 1)
            ->where('documents.0.asunto', 'Pendiente')
        );
    }

    /**
     * 4. Seguridad - ACCESO
     */
    public function test_non_admin_cannot_access_inbox()
    {
        $operator = User::factory()->create();

        $response = $this->actingAs($operator)->get('/bandeja');

        $response->assertStatus(403);
    }

    /**
     * 5. Integridad de Datos
     */
    public function test_inbox_can_only_update_storage_for_existing_boxes()
    {
        $block = Block::factory()->create(['box_id' => null]);

        $response = $this->actingAs($this->adminUser)->put("/inbox/update-storage/{$block->id}", [
            'n_section' => 1,
            'n_andamio' => 1,
            'n_box' => 9999, // Inexistente
        ]);

        $response->assertSessionHasErrors(['n_box']);
    }

    /**
     * 6. Resiliencia - BÚSQUEDA
     */
    public function test_inbox_handles_search_with_emojis()
    {
        Block::factory()->create(['box_id' => null, 'asunto' => 'Expediente 📁']);

        $response = $this->actingAs($this->adminUser)->get('/bandeja?search='.urlencode('📁'));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('documents', 1)
            );
    }

    /**
     * 7. Rendimiento - OPTIMIZACIÓN
     */
    public function test_inbox_index_is_optimized()
    {
        Block::factory()->count(5)->create(['box_id' => null]);

        \DB::enableQueryLog();
        $this->actingAs($this->adminUser)->get('/bandeja');
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        // Debe cargar relaciones (user, group, etc) eficientemente
        $this->assertLessThan(25, count($queries));
    }

    /**
     * 8. Eliminar Archivo Digital del Bloque
     */
    public function test_inbox_can_delete_block_file()
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        $fakePath = 'blocks/test/file.pdf';
        \Illuminate\Support\Facades\Storage::disk('local')->put($fakePath, 'dummy content');

        $block = Block::factory()->create([
            'box_id' => null,
            'root' => $fakePath,
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/inbox/delete-file/{$block->id}");

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Archivo del documento eliminado correctamente.');

        $block->refresh();
        $this->assertNull($block->root);
        \Illuminate\Support\Facades\Storage::disk('local')->assertMissing($fakePath);
    }

    /**
     * 9. Error al subir archivo que supera el tamaño máximo permitido por el entorno
     */
    public function test_inbox_update_storage_fails_when_file_exceeds_max_size()
    {
        $section = Section::factory()->create();
        $andamio = Andamio::factory()->create(['section_id' => $section->id]);
        $box = Box::factory()->create(['andamio_id' => $andamio->id]);
        $block = Block::factory()->create(['box_id' => null]);

        $maxKb = \App\Support\PhpIniHelper::getMaxUploadFileSizeInKilobytes();
        $fakeFile = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', $maxKb + 500, 'application/pdf');

        $response = $this->actingAs($this->adminUser)->put("/inbox/update-storage/{$block->id}", [
            'n_section' => $section->id,
            'n_andamio' => $andamio->id,
            'n_box' => $box->id,
            'root' => $fakeFile,
        ]);

        $response->assertSessionHasErrors(['root']);
        $errors = session('errors')->get('root');
        $this->assertStringContainsString('supera el tamaño máximo permitido por el entorno', $errors[0]);
    }

    /**
     * 10. Error cuando php.ini rechaza el archivo con UPLOAD_ERR_INI_SIZE
     */
    public function test_inbox_update_storage_fails_with_upload_err_ini_size()
    {
        $section = Section::factory()->create();
        $andamio = Andamio::factory()->create(['section_id' => $section->id]);
        $box = Box::factory()->create(['andamio_id' => $andamio->id]);
        $block = Block::factory()->create(['box_id' => null]);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_upload');
        file_put_contents($tempFile, 'dummy content');

        $fakeFile = new \Illuminate\Http\UploadedFile(
            $tempFile,
            'massive_file.pdf',
            'application/pdf',
            UPLOAD_ERR_INI_SIZE,
            true
        );

        $response = $this->actingAs($this->adminUser)->put("/inbox/update-storage/{$block->id}", [
            'n_section' => $section->id,
            'n_andamio' => $andamio->id,
            'n_box' => $box->id,
            'root' => $fakeFile,
        ]);

        $response->assertSessionHasErrors(['root']);
        $errors = session('errors')->get('root');
        $this->assertStringContainsString('supera el tamaño máximo permitido por el entorno', $errors[0]);

        if (file_exists($tempFile)) {
            @unlink($tempFile);
        }
    }

    /**
     * 11. Éxito al actualizar almacenamiento con un archivo válido dentro del límite
     */
    public function test_inbox_update_storage_succeeds_with_valid_file()
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        \Illuminate\Support\Facades\Storage::fake('public');

        $section = Section::factory()->create();
        $andamio = Andamio::factory()->create(['section_id' => $section->id]);
        $box = Box::factory()->create(['andamio_id' => $andamio->id]);
        $block = Block::factory()->create(['box_id' => null]);

        $fakeFile = \Illuminate\Http\UploadedFile::fake()->create('valid_document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->adminUser)->put("/inbox/update-storage/{$block->id}", [
            'n_section' => $section->id,
            'n_andamio' => $andamio->id,
            'n_box' => $box->id,
            'root' => $fakeFile,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('message', 'Información de almacenamiento actualizada correctamente.');

        $block->refresh();
        $this->assertSame($box->id, $block->box_id);
        $this->assertNotNull($block->root);
    }

    /**
     * 12. Búsqueda avanzada por serie documental, n_bloque, rango de folios y periodo
     */
    public function test_inbox_can_filter_by_documentary_series_code_and_name(): void
    {
        $series1 = \App\Models\DocumentarySeries::create([
            'codigo' => 'SER-CONTRATOS',
            'nombre' => 'Contratos de Personal',
        ]);
        $series2 = \App\Models\DocumentarySeries::create([
            'codigo' => 'SER-PLANILLAS',
            'nombre' => 'Planillas de Remuneraciones',
        ]);

        $block1 = Block::factory()->create([
            'box_id' => null,
            'n_bloque' => 101,
            'documentary_series_id' => $series1->id,
            'asunto' => 'Expediente A',
        ]);
        $block1->periods()->create([
            'rango_inicial' => 1,
            'rango_final' => 10,
            'periodo' => 2024,
        ]);

        $block2 = Block::factory()->create([
            'box_id' => null,
            'n_bloque' => 102,
            'documentary_series_id' => $series2->id,
            'asunto' => 'Expediente B',
        ]);
        $block2->periods()->create([
            'rango_inicial' => 11,
            'rango_final' => 20,
            'periodo' => 2025,
        ]);

        // Filtro por código de serie
        $response = $this->actingAs($this->adminUser)->get('/bandeja?search=CONTRATOS');
        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('documents', 1)
                ->where('documents.0.id', $block1->id)
            );

        // Filtro por nombre de serie
        $response2 = $this->actingAs($this->adminUser)->get('/bandeja?search=Planillas');
        $response2->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('documents', 1)
                ->where('documents.0.id', $block2->id)
            );
    }

    public function test_inbox_can_filter_by_n_bloque_and_numeric_folio_ranges(): void
    {
        $block1 = Block::factory()
            ->withPeriods([
                ['rango_inicial' => 1, 'rango_final' => 20, 'periodo' => 2006],
            ])
            ->create([
                'box_id' => null,
                'n_bloque' => 905,
                'asunto' => 'Bloque A',
                'periodo' => 2006,
            ]);

        $block2 = Block::factory()
            ->withPeriods([
                ['rango_inicial' => 51, 'rango_final' => 100, 'periodo' => 2026],
            ])
            ->create([
                'box_id' => null,
                'n_bloque' => 999,
                'asunto' => 'Bloque B',
                'periodo' => 2026,
            ]);

        // 1. Filtrar por n_bloque
        $response1 = $this->actingAs($this->adminUser)->get('/bandeja?search=905');
        $response1->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('documents', 1)
                ->where('documents.0.id', $block1->id)
            );

        // 2. Filtrar por número que cae dentro del rango de folios (2 cae en 1-20 y periodo 2006)
        $response2 = $this->actingAs($this->adminUser)->get('/bandeja?search=2');
        $response2->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('documents', 1)
                ->where('documents.0.id', $block1->id)
            );

        // 3. Filtrar por búsqueda combinada de folio y periodo: search=2 y periodo=2006
        $responseCombined = $this->actingAs($this->adminUser)->get('/bandeja?search=2&periodo=2006');
        $responseCombined->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('documents', 1)
                ->where('documents.0.id', $block1->id)
            );

        // 4. Filtrar por texto combinado "2 2006"
        $responseCombinedText = $this->actingAs($this->adminUser)->get('/bandeja?search=2 2006');
        $responseCombinedText->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('documents', 1)
                ->where('documents.0.id', $block1->id)
            );

        // 5. Filtrar por periodo
        $response3 = $this->actingAs($this->adminUser)->get('/bandeja?periodo=2026');
        $response3->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('documents', 1)
                ->where('documents.0.id', $block2->id)
            );
    }
}
