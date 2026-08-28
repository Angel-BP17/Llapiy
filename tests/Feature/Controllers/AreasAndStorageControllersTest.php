<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Area;
use App\Models\Section;
use App\Models\Andamio;
use App\Models\Box;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AreasAndStorageControllersTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        
        Permission::firstOrCreate(['name' => 'areas.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'areas.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'sections.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'sections.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'andamios.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'andamios.create', 'guard_name' => 'web']);

        $adminRole->givePermissionTo(Permission::all());

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    /**
     * 1. ÁREAS
     */
    public function test_area_index_returns_consistent_contract()
    {
        Area::factory()->count(2)->create();

        $response = $this->actingAs($this->adminUser)->get('/areas');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('areas/index')
                ->has('areas')
            );
    }

    public function test_area_store_creates_real_database_records()
    {
        $data = [
            'descripcion' => 'Area de Test',
            'abreviacion' => 'AT'
        ];

        $response = $this->actingAs($this->adminUser)->post('/areas', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('areas', ['descripcion' => 'Area de Test']);
    }

    /**
     * 2. STORAGE (SECCIONES)
     */
    public function test_section_creation_fails_with_invalid_data()
    {
        $response = $this->actingAs($this->adminUser)->post('/sections', [
            'n_section' => '',
            'descripcion' => ''
        ]);

        $response->assertSessionHasErrors(['n_section', 'descripcion']);
    }

    public function test_operator_cannot_create_areas()
    {
        $operator = User::factory()->create();

        $response = $this->actingAs($operator)->post('/areas', [
            'descripcion' => 'Intento',
            'abreviacion' => 'INT'
        ]);

        $response->assertStatus(403);
    }

    /**
     * 3. ANDAMIOS
     */
    public function test_andamio_creation_requires_valid_section()
    {
        $section = Section::factory()->create();
        
        $response = $this->actingAs($this->adminUser)->post("/sections/{$section->id}/andamios", [
            'n_andamio' => 1,
            'descripcion' => 'Andamio A'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('andamios', ['n_andamio' => 1, 'section_id' => $section->id]);
    }

    /**
     * 4. BOXES
     */
    public function test_box_creation_handles_duplicate_numbers_in_same_andamio()
    {
        $section = Section::factory()->create();
        $andamio = Andamio::factory()->create(['section_id' => $section->id]);
        Box::factory()->create(['n_box' => 'BOX-01', 'andamio_id' => $andamio->id]);

        $response = $this->actingAs($this->adminUser)->post("/sections/{$section->id}/andamios/{$andamio->id}/boxes", [
            'n_box' => 'BOX-01'
        ]);

        $response->assertSessionHasErrors(['n_box']);
    }

    /**
     * 5. RENDIMIENTO
     */
    public function test_area_index_is_not_slow()
    {
        Area::factory()->count(5)->create();

        \DB::enableQueryLog();
        $this->actingAs($this->adminUser)->get('/areas');
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        $this->assertLessThan(20, count($queries));
    }
}
