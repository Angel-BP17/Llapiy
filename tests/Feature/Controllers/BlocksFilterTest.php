<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Block;
use App\Models\Area;
use App\Models\Group;
use App\Models\Subgroup;
use App\Models\Section;
use App\Models\Andamio;
use App\Models\Box;
use App\Models\GroupType;
use App\Models\AreaGroupType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
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
}
