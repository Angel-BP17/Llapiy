<?php

namespace Tests\Feature\Controllers;

use App\Models\Andamio;
use App\Models\Area;
use App\Models\Block;
use App\Models\Box;
use App\Models\DocumentarySeries;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
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
            'abreviacion' => 'AT',
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
            'descripcion' => '',
        ]);

        $response->assertSessionHasErrors(['n_section', 'descripcion']);
    }

    public function test_operator_cannot_create_areas()
    {
        $operator = User::factory()->create();

        $response = $this->actingAs($operator)->post('/areas', [
            'descripcion' => 'Intento',
            'abreviacion' => 'INT',
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
            'descripcion' => 'Andamio A',
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
            'n_box' => 'BOX-01',
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

    /**
     * 6. CONTADORES Y REPORTES DE ALMACÉN
     */
    public function test_section_index_returns_storage_stats()
    {
        Section::factory()->create();

        $response = $this->actingAs($this->adminUser)->get('/sections');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('storage/index')
                ->has('stats')
                ->has('stats.totalBlocks')
                ->has('stats.archivedBlocks')
                ->has('stats.indexedBlocks')
                ->has('stats.filledBoxes')
            );
    }

    public function test_storage_pdf_report_generates_successfully()
    {
        $response = $this->actingAs($this->adminUser)->get('/sections/report');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_cajas_archivos_index_loads_documentary_series_for_blocks(): void
    {
        $section = Section::factory()->create();
        $andamio = Andamio::factory()->create(['section_id' => $section->id]);
        $box = Box::factory()->create(['andamio_id' => $andamio->id]);
        $series = DocumentarySeries::create([
            'codigo' => 'SER-TEST',
            'nombre' => 'Serie de Prueba',
            'descripcion' => 'Descripción de prueba',
            'retencion_anios' => 5,
        ]);

        $block = Block::factory()->create([
            'box_id' => $box->id,
            'documentary_series_id' => $series->id,
            'asunto' => 'Bloque con Serie Documental',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get("/sections/{$section->id}/andamios/{$andamio->id}/boxes/{$box->id}/archivos");

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('storage/index')
                ->where('level', 'archivos')
                ->has('archivos', 1)
                ->where('archivos.0.id', $block->id)
                ->where('archivos.0.documentary_series_id', $series->id)
                ->where('archivos.0.documentary_series.codigo', 'SER-TEST')
                ->where('archivos.0.documentary_series.nombre', 'Serie de Prueba')
            );
    }

    public function test_storage_levels_filter_by_periodo_successfully(): void
    {
        // Sección 1 con bloque de 2006
        $section1 = Section::factory()->create(['n_section' => 'S1']);
        $andamio1 = Andamio::factory()->create(['section_id' => $section1->id, 'n_andamio' => 101]);
        $box1 = Box::factory()->create(['andamio_id' => $andamio1->id, 'n_box' => 'B-01']);
        $block1 = Block::factory()->create([
            'box_id' => $box1->id,
            'rango_inicial' => 1,
            'rango_final' => 60,
            'periodo' => 2006,
        ]);
        $block1->periods()->create([
            'rango_inicial' => 1,
            'rango_final' => 60,
            'periodo' => 2006,
        ]);

        // Sección 2 con bloque de 2007
        $section2 = Section::factory()->create(['n_section' => 'S2']);
        $andamio2 = Andamio::factory()->create(['section_id' => $section2->id, 'n_andamio' => 201]);
        $box2 = Box::factory()->create(['andamio_id' => $andamio2->id, 'n_box' => 'B-02']);
        $block2 = Block::factory()->create([
            'box_id' => $box2->id,
            'rango_inicial' => 61,
            'rango_final' => 195,
            'periodo' => 2007,
        ]);
        $block2->periods()->create([
            'rango_inicial' => 61,
            'rango_final' => 195,
            'periodo' => 2007,
        ]);

        // 1. Nivel /sections?periodo=2006
        $responseSections = $this->actingAs($this->adminUser)->get('/sections?periodo=2006');
        $responseSections->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('storage/index')
                ->where('level', 'sections')
                ->has('sections', 1)
                ->where('sections.0.id', $section1->id)
                ->where('filters.periodo', '2006')
                ->has('years')
            );

        // 2. Nivel /sections/{section1}/andamios?periodo=2006
        $responseAndamios = $this->actingAs($this->adminUser)->get("/sections/{$section1->id}/andamios?periodo=2006");
        $responseAndamios->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('storage/index')
                ->where('level', 'andamios')
                ->has('andamios', 1)
                ->where('andamios.0.id', $andamio1->id)
                ->where('filters.periodo', '2006')
            );

        // 3. Nivel /sections/{section1}/andamios/{andamio1}/boxes?periodo=2006
        $responseBoxes = $this->actingAs($this->adminUser)->get("/sections/{$section1->id}/andamios/{$andamio1->id}/boxes?periodo=2006");
        $responseBoxes->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('storage/index')
                ->where('level', 'boxes')
                ->has('boxes', 1)
                ->where('boxes.0.id', $box1->id)
                ->where('filters.periodo', '2006')
            );

        // 4. Nivel /sections/{section1}/andamios/{andamio1}/boxes/{box1}/archivos?periodo=2006
        $responseArchivos = $this->actingAs($this->adminUser)->get("/sections/{$section1->id}/andamios/{$andamio1->id}/boxes/{$box1->id}/archivos?periodo=2006");
        $responseArchivos->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('storage/index')
                ->where('level', 'archivos')
                ->has('archivos', 1)
                ->where('archivos.0.id', $block1->id)
                ->where('filters.periodo', '2006')
            );

        // Nivel archivos con periodo no existente en esa caja debe devolver 0
        $responseArchivosEmpty = $this->actingAs($this->adminUser)->get("/sections/{$section1->id}/andamios/{$andamio1->id}/boxes/{$box1->id}/archivos?periodo=2007");
        $responseArchivosEmpty->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('storage/index')
                ->where('level', 'archivos')
                ->has('archivos', 0)
            );
    }
}
