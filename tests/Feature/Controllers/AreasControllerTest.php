<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Area;
use App\Models\Group;
use App\Models\GroupType;
use App\Models\AreaGroupType;
use App\Models\Subgroup;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AreasControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('ADMINISTRADOR');
    }

    /** @test */
    public function admin_can_access_areas_index()
    {
        Area::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get('/areas');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('areas/index')
                ->has('areas', 3)
            );
    }

    /** @test */
    public function admin_can_create_area()
    {
        $data = ['descripcion' => 'Area Nueva', 'abreviacion' => 'AN'];

        $response = $this->actingAs($this->admin)->post('/areas', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('areas', $data);
    }

    /** @test */
    public function delete_area_cascades_to_groups_and_subgroups_and_sets_null_on_users_and_documents()
    {
        $area = Area::factory()->create();
        $gt = GroupType::factory()->create();
        $agt = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $gt->id]);
        $group = Group::factory()->create(['area_group_type_id' => $agt->id]);

        $subgroup = Subgroup::create([
            'group_id' => $group->id,
            'descripcion' => 'Subgrupo Test',
            'abreviacion' => 'ST',
        ]);

        $user = User::factory()->create([
            'group_id' => $group->id,
            'subgroup_id' => $subgroup->id,
        ]);

        $document = Document::factory()->create([
            'group_id' => $group->id,
            'subgroup_id' => $subgroup->id,
        ]);

        $response = $this->actingAs($this->admin)->delete("/areas/{$area->id}");

        $response->assertRedirect();

        // El área, el grupo y el subgrupo deben borrarse en cascada
        $this->assertDatabaseMissing('areas', ['id' => $area->id]);
        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
        $this->assertDatabaseMissing('subgroups', ['id' => $subgroup->id]);

        // Los usuarios y documentos deben conservarse pero con relaciones en null
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'group_id' => null,
            'subgroup_id' => null,
        ]);

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'group_id' => null,
            'subgroup_id' => null,
        ]);
    }

    /** @test */
    public function delete_group_cascades_to_subgroups_and_sets_null_on_users_and_documents()
    {
        $area = Area::factory()->create();
        $gt = GroupType::factory()->create();
        $agt = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $gt->id]);
        $group = Group::factory()->create(['area_group_type_id' => $agt->id]);

        $subgroup = Subgroup::create([
            'group_id' => $group->id,
            'descripcion' => 'Subgrupo Test',
            'abreviacion' => 'ST',
        ]);

        $user = User::factory()->create([
            'group_id' => $group->id,
            'subgroup_id' => $subgroup->id,
        ]);

        $document = Document::factory()->create([
            'group_id' => $group->id,
            'subgroup_id' => $subgroup->id,
        ]);

        $response = $this->actingAs($this->admin)->delete("/groups/{$group->id}");

        $response->assertRedirect();

        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
        $this->assertDatabaseMissing('subgroups', ['id' => $subgroup->id]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'group_id' => null,
            'subgroup_id' => null,
        ]);

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'group_id' => null,
            'subgroup_id' => null,
        ]);
    }

    /** @test */
    public function delete_subgroup_cascades_to_child_subgroups_and_sets_null_on_users_and_documents()
    {
        $area = Area::factory()->create();
        $gt = GroupType::factory()->create();
        $agt = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $gt->id]);
        $group = Group::factory()->create(['area_group_type_id' => $agt->id]);

        $parentSubgroup = Subgroup::create([
            'group_id' => $group->id,
            'descripcion' => 'Subgrupo Padre',
            'abreviacion' => 'SP',
        ]);

        $childSubgroup = Subgroup::create([
            'group_id' => $group->id,
            'descripcion' => 'Subgrupo Hijo',
            'abreviacion' => 'SH',
            'parent_subgroup_id' => $parentSubgroup->id,
        ]);

        $user = User::factory()->create([
            'group_id' => $group->id,
            'subgroup_id' => $parentSubgroup->id,
        ]);

        $document = Document::factory()->create([
            'group_id' => $group->id,
            'subgroup_id' => $childSubgroup->id,
        ]);

        $response = $this->actingAs($this->admin)->delete("/subgroups/{$parentSubgroup->id}");

        $response->assertRedirect();

        $this->assertDatabaseMissing('subgroups', ['id' => $parentSubgroup->id]);
        $this->assertDatabaseMissing('subgroups', ['id' => $childSubgroup->id]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'subgroup_id' => null,
        ]);

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'subgroup_id' => null,
        ]);
    }
}
