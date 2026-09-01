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
        \Illuminate\Support\Facades\Storage::fake('public');
        $fakePath = 'blocks/test/file.pdf';
        \Illuminate\Support\Facades\Storage::disk('public')->put($fakePath, 'dummy content');

        $block = Block::factory()->create([
            'box_id' => null,
            'root' => $fakePath,
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/inbox/delete-file/{$block->id}");

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Archivo del documento eliminado correctamente.');

        $block->refresh();
        $this->assertNull($block->root);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($fakePath);
    }
}
