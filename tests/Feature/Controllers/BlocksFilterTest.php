<?php

namespace Tests\Feature\Controllers;

use App\Models\Andamio;
use App\Models\Block;
use App\Models\Box;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BlocksFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);

        Permission::firstOrCreate(['name' => 'view-blocks', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'blocks.view.all', 'guard_name' => 'web']);

        $adminRole->givePermissionTo(Permission::all());

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    public function test_can_filter_blocks_by_n_bloque()
    {
        Block::factory()->create(['n_bloque' => 12345, 'asunto' => 'First Block']);
        Block::factory()->create(['n_bloque' => 67890, 'asunto' => 'Second Block']);

        $response = $this->actingAs($this->adminUser)->get('/bloques?n_bloque=123');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('blocks/index')
            ->has('blocks', 1)
            ->where('blocks.0.n_bloque', 12345)
        );
    }

    public function test_can_filter_blocks_by_physical_location()
    {
        $section1 = Section::factory()->create(['n_section' => 'SEC-01']);
        $section2 = Section::factory()->create(['n_section' => 'SEC-02']);

        $andamio1 = Andamio::factory()->create(['section_id' => $section1->id, 'n_andamio' => 'AND-01']);
        $andamio2 = Andamio::factory()->create(['section_id' => $section2->id, 'n_andamio' => 'AND-02']);

        $box1 = Box::factory()->create(['andamio_id' => $andamio1->id, 'n_box' => 'BOX-01']);
        $box2 = Box::factory()->create(['andamio_id' => $andamio2->id, 'n_box' => 'BOX-02']);

        Block::factory()->create(['box_id' => $box1->id, 'asunto' => 'Block in Box 1']);
        Block::factory()->create(['box_id' => $box2->id, 'asunto' => 'Block in Box 2']);

        // Filtrar por section_id
        $response = $this->actingAs($this->adminUser)->get("/bloques?section_id={$section1->id}");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('blocks/index')
            ->has('blocks', 1)
            ->where('blocks.0.asunto', 'Block in Box 1')
        );

        // Filtrar por andamio_id
        $response = $this->actingAs($this->adminUser)->get("/bloques?andamio_id={$andamio2->id}");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('blocks/index')
            ->has('blocks', 1)
            ->where('blocks.0.asunto', 'Block in Box 2')
        );

        // Filtrar por box_id
        $response = $this->actingAs($this->adminUser)->get("/bloques?box_id={$box1->id}");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('blocks/index')
            ->has('blocks', 1)
            ->where('blocks.0.asunto', 'Block in Box 1')
        );
    }

    public function test_can_create_block_with_documentary_series()
    {
        $series = \App\Models\DocumentarySeries::factory()->create();

        $data = [
            'asunto' => 'Test block with series',
            'folios' => '10',
            'fecha' => '2026-07-26',
            'rango_inicial' => 1,
            'rango_final' => 50,
            'documentary_series_id' => $series->id,
        ];

        $response = $this->actingAs($this->adminUser)->post('/bloques', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('blocks', [
            'n_bloque' => 1,
            'documentary_series_id' => $series->id,
        ]);
    }

    public function test_can_create_and_filter_block_with_multiple_period_ranges(): void
    {
        $data = [
            'asunto' => 'Bloque Multi Periodo 2006-2007',
            'folios' => '195',
            'fecha' => '2006-05-10',
            'rango_inicial' => 1,
            'rango_final' => 195,
            'periods' => [
                [
                    'rango_inicial' => 1,
                    'rango_final' => 60,
                    'periodo' => 2006,
                ],
                [
                    'rango_inicial' => 61,
                    'rango_final' => 195,
                    'periodo' => 2007,
                ],
            ],
        ];

        $response = $this->actingAs($this->adminUser)->post('/bloques', $data);
        $response->assertRedirect();

        $this->assertDatabaseHas('blocks', [
            'asunto' => 'Bloque Multi Periodo 2006-2007',
            'rango_inicial' => '1',
            'rango_final' => '195',
        ]);

        $block = Block::where('asunto', 'Bloque Multi Periodo 2006-2007')->firstOrFail();

        $this->assertDatabaseHas('block_periods', [
            'block_id' => $block->id,
            'rango_inicial' => 1,
            'rango_final' => 60,
            'periodo' => 2006,
        ]);

        $this->assertDatabaseHas('block_periods', [
            'block_id' => $block->id,
            'rango_inicial' => 61,
            'rango_final' => 195,
            'periodo' => 2007,
        ]);

        // 1. Filtrar por 2006 -> Debe encontrarlo
        $res2006 = $this->actingAs($this->adminUser)->get('/bloques?year=2006');
        $res2006->assertStatus(200);
        $res2006->assertInertia(fn ($page) => $page
            ->component('blocks/index')
            ->has('blocks', 1)
            ->where('blocks.0.id', $block->id)
            ->has('blocks.0.periods', 2)
        );

        // 2. Filtrar por 2007 -> Debe encontrarlo también gracias a su sub-rango
        $res2007 = $this->actingAs($this->adminUser)->get('/bloques?year=2007');
        $res2007->assertStatus(200);
        $res2007->assertInertia(fn ($page) => $page
            ->component('blocks/index')
            ->has('blocks', 1)
            ->where('blocks.0.id', $block->id)
        );

        // 3. Filtrar por 2008 -> NO debe encontrarlo
        $res2008 = $this->actingAs($this->adminUser)->get('/bloques?year=2008');
        $res2008->assertStatus(200);
        $res2008->assertInertia(fn ($page) => $page
            ->component('blocks/index')
            ->has('blocks', 0)
        );
    }

    public function test_storage_box_and_global_search_finds_block_by_range_period(): void
    {
        $section = Section::factory()->create();
        $andamio = Andamio::factory()->create(['section_id' => $section->id]);
        $box = Box::factory()->create(['andamio_id' => $andamio->id]);

        $block = Block::factory()->create([
            'box_id' => $box->id,
            'asunto' => 'Bloque en Almacen con dos anios',
            'rango_inicial' => 1,
            'rango_final' => 195,
            'periodo' => 2006,
        ]);

        $block->periods()->delete();
        $block->periods()->create([
            'rango_inicial' => 1,
            'rango_final' => 60,
            'periodo' => 2006,
        ]);
        $block->periods()->create([
            'rango_inicial' => 61,
            'rango_final' => 195,
            'periodo' => 2007,
        ]);

        // Buscar en la caja por 2007
        $response = $this->actingAs($this->adminUser)
            ->get("/sections/{$section->id}/andamios/{$andamio->id}/boxes/{$box->id}/archivos?search=2007");

        $response->assertStatus(200)
            ->assertInertia(fn ($page) => $page
                ->component('storage/index')
                ->where('level', 'archivos')
                ->has('archivos', 1)
                ->where('archivos.0.id', $block->id)
            );

        // Buscar en el buscador general de secciones por 2007
        $searchResponse = $this->actingAs($this->adminUser)->get('/sections?search=2007');
        $searchResponse->assertStatus(200)
            ->assertInertia(fn ($page) => $page
                ->component('storage/index')
                ->has('searchedBlocks', 1)
                ->where('searchedBlocks.0.id', $block->id)
            );
    }
}
