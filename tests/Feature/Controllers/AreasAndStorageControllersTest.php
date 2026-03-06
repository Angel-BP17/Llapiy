<?php

namespace Tests\Feature\Controllers;

use App\Models\Area;
use App\Models\Group;
use App\Models\GroupType;
use App\Models\AreaGroupType;
use App\Models\Section;
use App\Models\Andamio;
use App\Models\Box;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AreasAndStorageControllersTest extends TestCase
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
     * 1. Etapa de Contrato (API Schema) - ÁREAS
     */
    public function test_area_index_returns_consistent_contract()
    {
        Area::factory()->count(2)->create();

        $response = $this->actingAs($this->adminUser)->getJson('/api/areas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'areas' => [
                        'data' => [
                            '*' => ['id', 'descripcion', 'abreviacion', 'groups']
                        ]
                    ]
                ]
            ]);
    }

    /**
     * 2. Etapa de Validación (Gatekeeping) - SECCIONES
     */
    public function test_section_creation_fails_with_invalid_data()
    {
        $response = $this->actingAs($this->adminUser)->postJson('/api/sections', [
            'n_section' => '', // Requerido
            'descripcion' => str_repeat('Too Long ', 100),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['n_section', 'descripcion']);
    }

    /**
     * 4. Etapa de Integridad - ÁREAS Y GRUPOS
     */
    public function test_area_store_creates_real_database_records_and_returns_object()
    {
        $areaData = [
            'descripcion' => 'Nueva Area Integridad',
            'abreviacion' => 'NAI',
            'grupos' => [
                ['descripcion' => 'Grupo 1', 'abreviacion' => 'G1']
            ]
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/areas', $areaData);

        $response->assertStatus(201)
            ->assertJsonPath('data.area.descripcion', 'Nueva Area Integridad');

        $this->assertDatabaseHas('areas', ['descripcion' => 'Nueva Area Integridad']);
        $this->assertDatabaseHas('groups', ['descripcion' => 'Grupo 1']);
    }

    /**
     * 5. Etapa de Reglas de Negocio - ALMACENAMIENTO
     */
    public function test_andamio_creation_requires_valid_section()
    {
        $section = Section::factory()->create();
        
        $response = $this->actingAs($this->adminUser)
            ->postJson("/api/sections/{$section->id}/andamios", [
                'n_andamio' => 5,
                'descripcion' => 'Andamio Test'
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.andamio.n_andamio', 5);
            
        $this->assertDatabaseHas('andamios', ['section_id' => $section->id, 'n_andamio' => 5]);
    }

    /**
     * 3. Etapa de Seguridad (RBAC)
     */
    public function test_operator_cannot_create_areas()
    {
        $operator = User::factory()->create();
        $response = $this->actingAs($operator)->postJson('/api/areas', ['descripcion' => 'Intento']);
        $response->assertStatus(403);
    }

    /**
     * 6. Etapa de Manejo de Archivos (Storage)
     * Al crear un área, el modelo Area.php dispara un Observer/Evento que crea una carpeta.
     */
    public function test_area_creation_creates_physical_directory()
    {
        $this->withoutExceptionHandling();
        \Storage::fake('public');
        
        $this->actingAs($this->adminUser)->postJson('/api/areas', [
            'descripcion' => 'Carpeta Fisica',
            'abreviacion' => 'CF'
        ]);

        \Storage::disk('public')->assertExists('documents/Carpeta Fisica');
    }

    /**
     * 7. Etapa de Resiliencia - CAJAS
     */
    public function test_box_creation_handles_duplicate_numbers_in_same_andamio()
    {
        $section = Section::factory()->create();
        $andamio = Andamio::factory()->create(['section_id' => $section->id]);
        Box::factory()->create(['andamio_id' => $andamio->id, 'n_box' => 'C-100']);

        // Intentar crear la misma caja en el mismo andamio (Debe fallar por validación)
        $response = $this->actingAs($this->adminUser)
            ->postJson("/api/sections/{$section->id}/andamios/{$andamio->id}/boxes", [
                'n_box' => 'C-100'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['n_box']);
    }

    /**
     * 8. Etapa de Rendimiento - ÁREAS
     */
    public function test_area_index_is_not_slow_with_many_groups()
    {
        $area = Area::factory()->create();
        $gt = GroupType::factory()->create();
        $agt = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $gt->id]);
        Group::factory()->count(5)->create(['area_group_type_id' => $agt->id]);

        \DB::enableQueryLog();
        $this->actingAs($this->adminUser)->getJson('/api/areas');
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        // Al usar Eager Loading selectivo, el número de queries debe ser bajo y constante
        $this->assertLessThan(15, count($queries));
    }
}
