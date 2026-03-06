<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Block;
use App\Models\Area;
use App\Models\Box;
use App\Models\Andamio;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    /**
     * 1. Etapa de Contrato (API Schema)
     */
    public function test_inbox_index_returns_strict_contract()
    {
        Block::factory()->count(2)->create(['box_id' => null]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/inbox');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'documents' => [
                        'data' => [
                            '*' => ['id', 'n_bloque', 'asunto', 'user']
                        ]
                    ],
                    'areas', 'sections', 'andamios', 'boxes', 'attendedBlocksCount'
                ]
            ]);
    }

    /**
     * 2. Etapa de Validación (Gatekeeping)
     */
    public function test_inbox_update_storage_requires_valid_data()
    {
        $block = Block::factory()->create();

        $response = $this->actingAs($this->adminUser)->putJson("/api/inbox/update-storage/{$block->id}", [
            'n_box' => 'No es numero',
            'n_andamio' => '',
            'n_section' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['n_box', 'n_andamio', 'n_section']);
    }

    /**
     * 5. Etapa de Reglas de Negocio
     * Los bloques en la bandeja no deben tener caja asignada (withoutBox).
     */
    public function test_inbox_only_shows_blocks_without_box()
    {
        $box = Box::factory()->create();
        Block::factory()->create(['box_id' => $box->id]); // Ya guardado
        Block::factory()->create(['box_id' => null]); // En bandeja

        $response = $this->actingAs($this->adminUser)->getJson('/api/inbox');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.documents.data'));
    }

    /**
     * 3. Etapa de Seguridad (RBAC)
     */
    public function test_non_admin_cannot_access_inbox()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/inbox');
        $response->assertStatus(403);
    }

    /**
     * 6. Etapa de Manejo de Archivos (Storage)
     */
    public function test_inbox_can_only_update_storage_for_existing_boxes()
    {
        $block = Block::factory()->create();
        
        $response = $this->actingAs($this->adminUser)->putJson("/api/inbox/update-storage/{$block->id}", [
            'n_box' => 99999, // Caja inexistente
            'n_andamio' => 1,
            'n_section' => 1
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['n_box']);
    }

    /**
     * 7. Etapa de Resiliencia
     */
    public function test_inbox_handles_search_with_emojis()
    {
        Block::factory()->create(['asunto' => 'Expediente 🔥 Urgente', 'box_id' => null]);
        
        $response = $this->actingAs($this->adminUser)->getJson('/api/inbox?search=🔥');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.documents.data'));
    }

    /**
     * 8. Etapa de Rendimiento
     */
    public function test_inbox_index_is_optimized()
    {
        Block::factory()->count(10)->create(['box_id' => null]);
        
        \DB::enableQueryLog();
        $this->actingAs($this->adminUser)->getJson('/api/inbox');
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        // No debería haber consultas N+1 para cargar el usuario y área de cada bloque
        $this->assertLessThan(20, count($queries));
    }
}
