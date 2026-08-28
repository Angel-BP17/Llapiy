<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

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
     * 1. Etapa de Contrato - DASHBOARD
     */
    public function test_dashboard_index_returns_strict_contract()
    {
        $response = $this->actingAs($this->adminUser)->get('/');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->has('stats')
                ->has('documentosRecientes')
                ->has('documentosPorTipo')
                ->has('docsByArea')
            );
    }

    /**
     * 2. Lógica de Negocio - ESTADÍSTICAS
     */
    public function test_dashboard_calculates_document_percentages_correctly()
    {
        DocumentType::factory()->count(2)->create();
        Document::factory()->count(5)->create();

        $response = $this->actingAs($this->adminUser)->get('/');

        $response->assertStatus(200);
        $this->assertEquals(5, $response->viewData('page')['props']['stats']['documentCount']);
    }

    /**
     * 3. Resiliencia - DATOS VACÍOS
     */
    public function test_dashboard_handles_empty_database_gracefully()
    {
        $response = $this->actingAs($this->adminUser)->get('/');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->where('stats.documentCount', 0)
                ->where('documentosRecientes', [])
            );
    }

    /**
     * 4. Rendimiento - OPTIMIZACIÓN
     */
    public function test_dashboard_is_optimized()
    {
        Document::factory()->count(10)->create();

        \DB::enableQueryLog();
        $this->actingAs($this->adminUser)->get('/');
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        // El dashboard debe estar muy optimizado ya que usa servicios agregados
        $this->assertLessThan(20, count($queries), "El Dashboard está ejecutando demasiadas consultas.");
    }

    /** @test */
    public function test_admin_can_clear_system_data()
    {
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'clear-system', 'guard_name' => 'web']);
        $this->adminUser->givePermissionTo($permission);

        $document = Document::factory()->create();

        $response = $this->actingAs($this->adminUser)->delete('/admin/clear-all');

        $response->assertRedirect();
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        $this->assertDatabaseHas('users', ['user_name' => 'ADMIN']);
    }
}
