<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Document;
use App\Models\Group;
use App\Models\Subgroup;
use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\GroupType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentViewSegmentationTest extends TestCase
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
        Permission::firstOrCreate(['name' => 'documents.view.all', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'documents.view.own', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'documents.view.group', 'guard_name' => 'web']);
    }

    /** @test */
    public function a_user_with_view_own_only_sees_their_documents()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('documents.view.own');

        Document::factory()->create(['user_id' => $user->id, 'asunto' => 'Mi Documento']);
        Document::factory()->create(['asunto' => 'Documento Ajeno']);

        $response = $this->actingAs($user)->getJson('/api/documents');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.documents.data'));
        $this->assertEquals('Mi Documento', $response->json('data.documents.data.0.asunto'));
    }

    /** @test */
    public function a_user_with_view_group_sees_everything_in_their_group_if_no_subgroup()
    {
        $user = User::factory()->create(['group_id' => $this->group->id, 'subgroup_id' => null]);
        $user->givePermissionTo('documents.view.group');

        Document::factory()->create(['group_id' => $this->group->id, 'asunto' => 'Del Grupo']);
        Document::factory()->create(['asunto' => 'De Otro Grupo']);

        $response = $this->actingAs($user)->getJson('/api/documents');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.documents.data'));
        $this->assertEquals('Del Grupo', $response->json('data.documents.data.0.asunto'));
    }

    /** @test */
    public function a_user_with_view_group_sees_only_subgroup_documents_if_assigned_to_one()
    {
        $user = User::factory()->create(['group_id' => $this->group->id, 'subgroup_id' => $this->subgroup->id]);
        $user->givePermissionTo('documents.view.group');

        Document::factory()->create([
            'group_id' => $this->group->id, 
            'subgroup_id' => $this->subgroup->id, 
            'asunto' => 'Del Subgrupo'
        ]);
        
        Document::factory()->create([
            'group_id' => $this->group->id, 
            'subgroup_id' => null, 
            'asunto' => 'Del Grupo General'
        ]);

        $response = $this->actingAs($user)->getJson('/api/documents');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.documents.data'));
        $this->assertEquals('Del Subgrupo', $response->json('data.documents.data.0.asunto'));
    }
}
