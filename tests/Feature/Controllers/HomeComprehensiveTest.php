<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\Block;
use App\Models\DocumentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HomeComprehensiveTest extends TestCase
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
    public function test_dashboard_index_returns_strict_contract()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->adminUser)->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'userCount',
                    'documentCount',
                    'documentTypeCount',
                    'totalNoAlmacenados',
                    'documentosRecientes',
                    'documentosPorTipo',
                    'documentosPorMes'
                ]
            ]);
    }

    /**
     * 5. Etapa de Reglas de Negocio
     * Verificación de conteos y porcentajes.
     */
    public function test_dashboard_calculates_document_percentages_correctly()
    {
        $typeA = DocumentType::factory()->create(['name' => 'Tipo A']);
        $typeB = DocumentType::factory()->create(['name' => 'Tipo B']);

        Document::factory()->count(3)->create(['document_type_id' => $typeA->id]);
        Document::factory()->count(1)->create(['document_type_id' => $typeB->id]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/dashboard');

        $response->assertStatus(200);
        $stats = collect($response->json('data.documentosPorTipo'));

        $tipoAStat = $stats->firstWhere('tipo', 'Tipo A');
        $tipoBStat = $stats->firstWhere('tipo', 'Tipo B');

        // Total 4 docs. A=75%, B=25%
        $this->assertEquals(75, $tipoAStat['porcentaje']);
        $this->assertEquals(25, $tipoBStat['porcentaje']);
    }

    /**
     * 7. Etapa de Resiliencia
     * El dashboard debe funcionar incluso sin datos.
     */
    public function test_dashboard_handles_empty_database_gracefully()
    {
        // Solo el admin creado en setUp
        $response = $this->actingAs($this->adminUser)->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonPath('data.documentCount', 0)
            ->assertJsonPath('data.documentosPorTipo', []);
    }

    /**
     * 8. Etapa de Rendimiento
     */
    public function test_dashboard_is_optimized()
    {
        \DB::enableQueryLog();
        $this->actingAs($this->adminUser)->getJson('/api/dashboard');
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        $this->assertLessThan(15, count($queries), "El Dashboard está ejecutando demasiadas consultas.");
    }
}
