<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\Block;
use App\Models\Setting;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Subgroup;
use App\Models\Area;
use App\Models\GroupType;
use App\Models\AreaGroupType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColaboradorDocumentalDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $colaborador;
    protected Group $group1;
    protected Subgroup $subgroup1;
    protected AreaGroupType $agt;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $area = Area::factory()->create();
        $gt = GroupType::factory()->create();
        $this->agt = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $gt->id]);

        $this->group1 = Group::factory()->create(['area_group_type_id' => $this->agt->id]);
        $this->subgroup1 = Subgroup::create([
            'group_id' => $this->group1->id,
            'descripcion' => 'Subgrupo Test',
            'abreviacion' => 'SGT',
        ]);

        $this->colaborador = User::factory()->create([
            'group_id' => $this->group1->id,
            'subgroup_id' => $this->subgroup1->id,
        ]);
        $this->colaborador->assignRole('COLABORADOR_DOCUMENTAL');
    }

    /** @test */
    public function colaborador_can_access_configuration_page()
    {
        $response = $this->actingAs($this->colaborador)->get('/configuracion');
        $response->assertStatus(200);
    }

    /** @test */
    public function archivo_central_can_access_configuration_page()
    {
        $archivoCentralUser = User::factory()->create();
        $archivoCentralUser->assignRole('ARCHIVO_CENTRAL');

        $response = $this->actingAs($archivoCentralUser)->get('/configuracion');
        $response->assertStatus(200);
    }

    /** @test */
    public function colaborador_dashboard_only_monitors_same_workplace_documents_and_blocks()
    {
        // 1. Documents:
        // A document in the same group and subgroup (should count)
        Document::factory()->create([
            'user_id' => $this->colaborador->id,
            'group_id' => $this->group1->id,
            'subgroup_id' => $this->subgroup1->id,
        ]);

        // A document in another group/subgroup (should NOT count)
        $anotherGroup = Group::factory()->create(['area_group_type_id' => $this->agt->id]);
        Document::factory()->create([
            'group_id' => $anotherGroup->id,
            'subgroup_id' => null,
        ]);

        // 2. Blocks:
        // A block in the same group and subgroup (should count)
        Block::factory()->create([
            'user_id' => $this->colaborador->id,
            'group_id' => $this->group1->id,
            'subgroup_id' => $this->subgroup1->id,
            'box_id' => null,
        ]);

        // A block in another group (should NOT count)
        Block::factory()->create([
            'group_id' => $anotherGroup->id,
            'subgroup_id' => null,
            'box_id' => null,
        ]);

        $response = $this->actingAs($this->colaborador)->get('/');
        
        $response->assertStatus(200);
        $stats = $response->viewData('page')['props']['stats'];

        // Should count 1 document and 1 block of same workplace = 2
        $this->assertEquals(2, $stats['documentCount']);
        // Should count 1 block without box of same workplace = 1
        $this->assertEquals(1, $stats['totalNoAlmacenados']);
    }

    /** @test */
    public function colaborador_dashboard_only_shows_activity_stats_of_same_workplace_coworkers()
    {
        // Coworker (same group and subgroup)
        $coworker = User::factory()->create([
            'group_id' => $this->group1->id,
            'subgroup_id' => $this->subgroup1->id,
        ]);
        
        ActivityLog::create([
            'user_id' => $coworker->id,
            'action' => 'create',
            'model' => 'Document',
            'created_at' => now(),
        ]);

        // Another user (different group)
        $anotherGroup = Group::factory()->create(['area_group_type_id' => $this->agt->id]);
        $otherUser = User::factory()->create([
            'group_id' => $anotherGroup->id,
            'subgroup_id' => null,
        ]);

        ActivityLog::create([
            'user_id' => $otherUser->id,
            'action' => 'create',
            'model' => 'Document',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->colaborador)->get('/');
        
        $response->assertStatus(200);
        $activityStats = $response->viewData('page')['props']['activityStats'];

        // Should only show activity for coworker, not otherUser
        $this->assertCount(1, $activityStats);
        $this->assertEquals($coworker->name, $activityStats[0]['name']);
    }
}
