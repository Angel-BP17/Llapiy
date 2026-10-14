<?php

namespace Tests\Feature\Security;

use App\Models\Block;
use App\Models\DocumentarySeries;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlockLifecyclePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_common_user_can_update_and_delete_their_own_block_without_pdf(): void
    {
        $user = User::factory()->create();
        $user->assignRole('COLABORADOR_DOCUMENTAL');

        $block = Block::factory()->create([
            'user_id' => $user->id,
            'root' => null,
            'asunto' => 'Bloque Original',
            'folios' => 10,
        ]);

        $this->assertTrue($user->can('update', $block));
        $this->assertTrue($user->can('delete', $block));

        $updateResponse = $this->actingAs($user)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque Modificado',
            'folios' => '20',
            'fecha' => '2026-09-14',
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 20, 'periodo' => 2026],
            ],
        ]);

        $updateResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');
        $this->assertDatabaseHas('blocks', [
            'id' => $block->id,
            'asunto' => 'Bloque Modificado',
        ]);

        $deleteResponse = $this->actingAs($user)->delete("/bloques/{$block->id}");
        $deleteResponse->assertSessionHas('message', 'Bloque eliminado correctamente.');
        $this->assertDatabaseMissing('blocks', ['id' => $block->id]);
    }

    public function test_common_user_cannot_update_or_delete_block_when_it_has_pdf(): void
    {
        $user = User::factory()->create();
        $user->assignRole('COLABORADOR_DOCUMENTAL');

        $block = Block::factory()->create([
            'user_id' => $user->id,
            'root' => 'blocks/expediente_digitalizado.pdf',
            'asunto' => 'Bloque con PDF',
            'folios' => '10',
        ]);

        $this->assertFalse($user->can('update', $block));
        $this->assertFalse($user->can('delete', $block));

        $updateResponse = $this->actingAs($user)->put("/bloques/{$block->id}", [
            'asunto' => 'Intento Modificar',
            'folios' => '30',
            'fecha' => '2026-09-14',
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 30, 'periodo' => 2026],
            ],
        ]);

        $updateResponse->assertSessionHas('error', 'Ocurrio un error al editar el bloque.');
        $this->assertDatabaseHas('blocks', [
            'id' => $block->id,
            'asunto' => 'Bloque con PDF',
        ]);

        $deleteResponse = $this->actingAs($user)->delete("/bloques/{$block->id}");
        $deleteResponse->assertSessionHas('error', 'Ocurrio un error al eliminar el bloque.');
        $this->assertDatabaseHas('blocks', ['id' => $block->id]);
    }

    public function test_archivo_central_can_update_and_delete_block_with_pdf(): void
    {
        $archivoUser = User::factory()->create();
        $archivoUser->assignRole('ARCHIVO_CENTRAL');

        $block = Block::factory()->create([
            'root' => 'blocks/expediente_digitalizado.pdf',
            'asunto' => 'Bloque Digitalizado',
            'folios' => '15',
        ]);

        $this->assertTrue($archivoUser->can('update', $block));
        $this->assertTrue($archivoUser->can('delete', $block));

        $updateResponse = $this->actingAs($archivoUser)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque Actualizado por Archivo',
            'folios' => '18',
            'fecha' => '2026-09-14',
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 18, 'periodo' => 2026],
            ],
        ]);

        $updateResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');

        $deleteResponse = $this->actingAs($archivoUser)->delete("/bloques/{$block->id}");
        $deleteResponse->assertSessionHas('message', 'Bloque eliminado correctamente.');
        $this->assertDatabaseMissing('blocks', ['id' => $block->id]);
    }

    public function test_administrador_can_update_and_delete_block_with_pdf(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMINISTRADOR');

        $block = Block::factory()->create([
            'root' => 'blocks/expediente_digitalizado.pdf',
            'asunto' => 'Bloque para Admin',
            'folios' => '15',
        ]);

        $this->assertTrue($admin->can('update', $block));
        $this->assertTrue($admin->can('delete', $block));

        $updateResponse = $this->actingAs($admin)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque Actualizado por Admin',
            'folios' => '18',
            'fecha' => '2026-09-14',
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 18, 'periodo' => 2026],
            ],
        ]);

        $updateResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');

        $deleteResponse = $this->actingAs($admin)->delete("/bloques/{$block->id}");
        $deleteResponse->assertSessionHas('message', 'Bloque eliminado correctamente.');
        $this->assertDatabaseMissing('blocks', ['id' => $block->id]);
    }

    public function test_post_too_large_exception_redirects_with_friendly_error(): void
    {
        Route::middleware(['web'])->post('/test-post-too-large', function () {
            throw new PostTooLargeException;
        });

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/test-post-too-large', []);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'El archivo adjunto excede el tamaño máximo permitido por el servidor.');
    }

    public function test_block_creation_and_update_persists_documentary_series(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMINISTRADOR');

        $seriesA = DocumentarySeries::create([
            'codigo' => 'SER-A',
            'nombre' => 'Serie A',
            'descripcion' => 'Descripción A',
            'retencion_anios' => 5,
        ]);

        $seriesB = DocumentarySeries::create([
            'codigo' => 'SER-B',
            'nombre' => 'Serie B',
            'descripcion' => 'Descripción B',
            'retencion_anios' => 10,
        ]);

        // 1. Crear bloque con serie documental
        $createResponse = $this->actingAs($admin)->post('/bloques', [
            'asunto' => 'Bloque con Serie A',
            'folios' => '50',
            'fecha' => '2026-09-19',
            'documentary_series_id' => (string) $seriesA->id,
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 50, 'periodo' => 2026],
            ],
        ]);

        $createResponse->assertSessionHas('message', 'Bloque creado correctamente.');
        $this->assertDatabaseHas('blocks', [
            'asunto' => 'Bloque con Serie A',
            'documentary_series_id' => $seriesA->id,
        ]);

        $block = Block::where('asunto', 'Bloque con Serie A')->firstOrFail();

        // 2. Actualizar cambiando a otra serie documental
        $updateResponse = $this->actingAs($admin)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque con Serie B',
            'folios' => '50',
            'fecha' => '2026-09-19',
            'documentary_series_id' => (string) $seriesB->id,
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 50, 'periodo' => 2026],
            ],
        ]);

        $updateResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');
        $this->assertDatabaseHas('blocks', [
            'id' => $block->id,
            'asunto' => 'Bloque con Serie B',
            'documentary_series_id' => $seriesB->id,
        ]);

        // 3. Actualizar desvinculando serie documental (enviando vacío/null)
        $clearResponse = $this->actingAs($admin)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque Sin Serie',
            'folios' => '50',
            'fecha' => '2026-09-19',
            'documentary_series_id' => '',
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 50, 'periodo' => 2026],
            ],
        ]);

        $clearResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');
        $this->assertDatabaseHas('blocks', [
            'id' => $block->id,
            'asunto' => 'Bloque Sin Serie',
            'documentary_series_id' => null,
        ]);
    }

    public function test_block_update_can_modify_period_ranges(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMINISTRADOR');

        $block = Block::factory()->create([
            'asunto' => 'Bloque Inicial 2005',
            'folios' => '100',
            'fecha' => '2005-01-01',
            'periodo' => 2005,
        ]);

        $updateResponse = $this->actingAs($admin)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque Actualizado con 2 Periodos',
            'folios' => '100',
            'fecha' => '2005-01-01',
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 40, 'periodo' => 2005],
                ['rango_inicial' => 41, 'rango_final' => 100, 'periodo' => 2006],
            ],
        ]);

        $updateResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');

        $this->assertDatabaseHas('block_periods', [
            'block_id' => $block->id,
            'rango_inicial' => 1,
            'rango_final' => 40,
            'periodo' => 2005,
        ]);

        $this->assertDatabaseHas('block_periods', [
            'block_id' => $block->id,
            'rango_inicial' => 41,
            'rango_final' => 100,
            'periodo' => 2006,
        ]);
    }

    public function test_admin_can_update_block_n_bloque(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMINISTRADOR');

        $block = Block::factory()->create([
            'n_bloque' => 5,
            'asunto' => 'Bloque con n_bloque original',
            'folios' => '50',
            'fecha' => '2025-06-10',
            'periodo' => 2025,
        ]);

        $updateResponse = $this->actingAs($admin)->put("/bloques/{$block->id}", [
            'n_bloque' => 42,
            'asunto' => 'Bloque con n_bloque editado',
            'folios' => '50',
            'fecha' => '2025-06-10',
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 50, 'periodo' => 2025],
            ],
        ]);

        $updateResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');

        $this->assertDatabaseHas('blocks', [
            'id' => $block->id,
            'n_bloque' => 42,
            'asunto' => 'Bloque con n_bloque editado',
        ]);
    }

    public function test_archivo_central_can_update_block_pdf_file_via_form(): void
    {
        Storage::fake('local');

        $archivoUser = User::factory()->create();
        $archivoUser->assignRole('ARCHIVO_CENTRAL');

        $block = Block::factory()->create([
            'asunto' => 'Bloque con PDF existente',
            'root' => 'blocks/old_document.pdf',
            'fecha' => '2026-10-14',
            'folios' => '50',
            'periodo' => 2026,
        ]);

        Storage::disk('local')->put('blocks/old_document.pdf', 'dummy old content');

        $newPdf = UploadedFile::fake()->create('nuevo_expediente.pdf', 200, 'application/pdf');

        $response = $this->actingAs($archivoUser)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque con PDF actualizado',
            'fecha' => '2026-10-14',
            'folios' => '50',
            'root' => $newPdf,
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 50, 'periodo' => 2026],
            ],
        ]);

        $response->assertSessionHas('message', 'Bloque actualizado correctamente.');

        $block->refresh();
        $this->assertEquals('Bloque con PDF actualizado', $block->asunto);
        $this->assertNotNull($block->root);
        $this->assertNotEquals('blocks/old_document.pdf', $block->root);
        $this->assertTrue(Storage::disk('local')->exists($block->root));
        $this->assertFalse(Storage::disk('local')->exists('blocks/old_document.pdf'));
    }

    public function test_non_archivo_central_cannot_update_block_pdf_file(): void
    {
        Storage::fake('local');

        $colaborador = User::factory()->create();
        $colaborador->assignRole('COLABORADOR_DOCUMENTAL');

        $block = Block::factory()->create([
            'user_id' => $colaborador->id,
            'asunto' => 'Bloque propio de colaborador',
            'root' => null,
            'fecha' => '2026-10-14',
            'folios' => '30',
            'periodo' => 2026,
        ]);

        $newPdf = UploadedFile::fake()->create('intento_subida.pdf', 100, 'application/pdf');

        $response = $this->actingAs($colaborador)->put("/bloques/{$block->id}", [
            'asunto' => 'Intento con PDF',
            'fecha' => '2026-10-14',
            'folios' => '30',
            'root' => $newPdf,
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 30, 'periodo' => 2026],
            ],
        ]);

        $response->assertSessionHasErrors(['root']);
        $block->refresh();
        $this->assertNull($block->root);
    }

    public function test_archivo_central_can_create_block_with_pdf_file(): void
    {
        Storage::fake('local');

        $archivoUser = User::factory()->create();
        $archivoUser->assignRole('ARCHIVO_CENTRAL');

        $pdf = UploadedFile::fake()->create('expediente_inicial.pdf', 150, 'application/pdf');

        $response = $this->actingAs($archivoUser)->post('/bloques', [
            'asunto' => 'Bloque Creado con PDF',
            'fecha' => '2026-10-14',
            'folios' => '40',
            'root' => $pdf,
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 40, 'periodo' => 2026],
            ],
        ]);

        $response->assertSessionHas('message', 'Bloque creado correctamente.');

        $block = Block::where('asunto', 'Bloque Creado con PDF')->firstOrFail();
        $this->assertNotNull($block->root);
        $this->assertTrue(Storage::disk('local')->exists($block->root));
    }

    public function test_non_archivo_central_cannot_create_block_with_pdf_file(): void
    {
        Storage::fake('local');

        $colaborador = User::factory()->create();
        $colaborador->assignRole('COLABORADOR_DOCUMENTAL');

        $pdf = UploadedFile::fake()->create('intento_creacion.pdf', 100, 'application/pdf');

        $response = $this->actingAs($colaborador)->post('/bloques', [
            'asunto' => 'Bloque Creado sin Permiso PDF',
            'fecha' => '2026-10-14',
            'folios' => '25',
            'root' => $pdf,
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 25, 'periodo' => 2026],
            ],
        ]);

        $response->assertSessionHasErrors(['root']);
        $this->assertDatabaseMissing('blocks', [
            'asunto' => 'Bloque Creado sin Permiso PDF',
        ]);
    }

    public function test_administrador_can_create_and_update_block_pdf_file(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();
        $admin->assignRole('ADMINISTRADOR');

        // 1. Crear bloque con PDF como Administrador
        $pdf = UploadedFile::fake()->create('admin_expediente.pdf', 120, 'application/pdf');

        $createResponse = $this->actingAs($admin)->post('/bloques', [
            'asunto' => 'Bloque Admin con PDF',
            'fecha' => '2026-10-14',
            'folios' => '35',
            'root' => $pdf,
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 35, 'periodo' => 2026],
            ],
        ]);

        $createResponse->assertSessionHas('message', 'Bloque creado correctamente.');

        $block = Block::where('asunto', 'Bloque Admin con PDF')->firstOrFail();
        $this->assertNotNull($block->root);
        $this->assertTrue(Storage::disk('local')->exists($block->root));

        // 2. Actualizar bloque con nuevo PDF como Administrador
        $newPdf = UploadedFile::fake()->create('admin_actualizado.pdf', 140, 'application/pdf');

        $updateResponse = $this->actingAs($admin)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque Admin Actualizado',
            'fecha' => '2026-10-14',
            'folios' => '35',
            'root' => $newPdf,
            'periods' => [
                ['rango_inicial' => 1, 'rango_final' => 35, 'periodo' => 2026],
            ],
        ]);

        $updateResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');

        $block->refresh();
        $this->assertEquals('Bloque Admin Actualizado', $block->asunto);
        $this->assertTrue(Storage::disk('local')->exists($block->root));
    }
}
