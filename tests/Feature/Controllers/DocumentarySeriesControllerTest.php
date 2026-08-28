<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\DocumentarySeries;
use App\Models\Block;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DocumentarySeriesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear permisos y roles
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        $colaboradorRole = Role::firstOrCreate(['name' => 'COLABORADOR_DOCUMENTAL', 'guard_name' => 'web']);

        Permission::firstOrCreate(['name' => 'documentary-series.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'documentary-series.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'documentary-series.update', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'documentary-series.delete', 'guard_name' => 'web']);

        $adminRole->givePermissionTo(Permission::all());

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole($colaboradorRole);
    }

    /**
     * Test ADMINISTRADOR can access list and see records.
     */
    public function test_admin_can_access_documentary_series_index()
    {
        DocumentarySeries::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)->get('/series-documentales');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('documentary_series/index')
                ->has('documentarySeries')
            );
    }

    /**
     * Test regular user cannot access documentary series index (returns 403).
     */
    public function test_non_admin_cannot_access_documentary_series_index()
    {
        $response = $this->actingAs($this->regularUser)->get('/series-documentales');

        $response->assertStatus(403);
    }

    /**
     * Test ADMINISTRADOR can store new documentary series.
     */
    public function test_admin_can_store_documentary_series()
    {
        $data = [
            'codigo' => 'SEC-001',
            'nombre' => 'Serie de Secretarías',
        ];

        $response = $this->actingAs($this->adminUser)->post('/series-documentales', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('documentary_series', [
            'codigo' => 'SEC-001',
            'nombre' => 'Serie de Secretarías',
        ]);
    }

    /**
     * Test regular user cannot store documentary series (returns 403).
     */
    public function test_non_admin_cannot_store_documentary_series()
    {
        $data = [
            'codigo' => 'SEC-002',
            'nombre' => 'Serie Prohibida',
        ];

        $response = $this->actingAs($this->regularUser)->post('/series-documentales', $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('documentary_series', ['codigo' => 'SEC-002']);
    }

    /**
     * Test ADMINISTRADOR can update documentary series.
     */
    public function test_admin_can_update_documentary_series()
    {
        $series = DocumentarySeries::factory()->create([
            'codigo' => 'OLD-001',
            'nombre' => 'Nombre Viejo',
        ]);

        $data = [
            'codigo' => 'NEW-001',
            'nombre' => 'Nombre Nuevo',
        ];

        $response = $this->actingAs($this->adminUser)->put("/series-documentales/{$series->id}", $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('documentary_series', [
            'id' => $series->id,
            'codigo' => 'NEW-001',
            'nombre' => 'Nombre Nuevo',
        ]);
    }

    /**
     * Test ADMINISTRADOR can delete documentary series without blocks.
     */
    public function test_admin_can_delete_documentary_series()
    {
        $series = DocumentarySeries::factory()->create();

        $response = $this->actingAs($this->adminUser)->delete("/series-documentales/{$series->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('documentary_series', ['id' => $series->id]);
    }

    /**
     * Test ADMINISTRADOR cannot delete documentary series with blocks.
     */
    public function test_admin_cannot_delete_documentary_series_with_blocks()
    {
        $series = DocumentarySeries::factory()->create();
        Block::factory()->create([
            'documentary_series_id' => $series->id,
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/series-documentales/{$series->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('documentary_series', ['id' => $series->id]);
    }
}
