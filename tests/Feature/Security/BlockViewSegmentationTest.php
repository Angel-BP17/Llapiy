<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Block;
use App\Models\Group;
use App\Models\Subgroup;
use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\GroupType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BlockViewSegmentationTest extends TestCase
{
    use RefreshDatabase;

    protected $area;
    protected $group;
    protected $subgroup;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->area = Area::factory()->create();
        $gt = GroupType::factory()->create();
        $agt = AreaGroupType::create(['area_id' => $this->area->id, 'group_type_id' => $gt->id]);
        $this->group = Group::factory()->create(['area_group_type_id' => $agt->id]);
        $this->subgroup = Subgroup::create([
            'group_id' => $this->group->id,
            'descripcion' => 'Subgrupo Test',
            'abreviacion' => 'ST'
        ]);

        // Crear los permisos nuevos
        Permission::firstOrCreate(['name' => 'blocks.view.all', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'blocks.view.own', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'blocks.view.group', 'guard_name' => 'web']);
    }

    /** @test */
    public function a_user_with_view_own_only_sees_their_blocks()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('blocks.view.own');

        Block::factory()->create(['user_id' => $user->id, 'asunto' => 'Mi Bloque']);
        Block::factory()->create(['asunto' => 'Bloque Ajeno']);

        $response = $this->actingAs($user)->getJson('/api/blocks');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.blocks.data'));
        $this->assertEquals('Mi Bloque', $response->json('data.blocks.data.0.asunto'));
    }

    /** @test */
    public function a_user_with_view_group_sees_everything_in_their_group_if_no_subgroup()
    {
        $user = User::factory()->create(['group_id' => $this->group->id, 'subgroup_id' => null]);
        $user->givePermissionTo('blocks.view.group');

        Block::factory()->create(['group_id' => $this->group->id, 'asunto' => 'Bloque del Grupo']);
        Block::factory()->create(['asunto' => 'Bloque de Otro Grupo']);

        $response = $this->actingAs($user)->getJson('/api/blocks');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.blocks.data'));
        $this->assertEquals('Bloque del Grupo', $response->json('data.blocks.data.0.asunto'));
    }

    /** @test */
    public function a_user_with_view_group_sees_only_subgroup_blocks_if_assigned_to_one()
    {
        $user = User::factory()->create(['group_id' => $this->group->id, 'subgroup_id' => $this->subgroup->id]);
        $user->givePermissionTo('blocks.view.group');

        Block::factory()->create([
            'group_id' => $this->group->id, 
            'subgroup_id' => $this->subgroup->id, 
            'asunto' => 'Bloque del Subgrupo'
        ]);
        
        Block::factory()->create([
            'group_id' => $this->group->id, 
            'subgroup_id' => null, 
            'asunto' => 'Bloque del Grupo General'
        ]);

        $response = $this->actingAs($user)->getJson('/api/blocks');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.blocks.data'));
        $this->assertEquals('Bloque del Subgrupo', $response->json('data.blocks.data.0.asunto'));
    }
}
