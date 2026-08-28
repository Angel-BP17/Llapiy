<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Document;
use App\Models\Group;
use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\GroupType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuperAdminBypassTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_access_documents_from_other_groups()
    {
        // 1. Setup: Crear dos grupos distintos
        $area = Area::factory()->create();
        $gt = GroupType::factory()->create();
        $agt = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $gt->id]);
        
        $groupA = Group::factory()->create(['area_group_type_id' => $agt->id, 'descripcion' => 'Grupo A']);
        $groupB = Group::factory()->create(['area_group_type_id' => $agt->id, 'descripcion' => 'Grupo B']);

        // 2. Setup: Crear un Administrador en el Grupo A
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        $admin = User::factory()->create(['group_id' => $groupA->id]);
        $admin->assignRole($adminRole);

        // 3. Setup: Crear un Documento en el Grupo B
        $documentOfGroupB = Document::factory()->create(['group_id' => $groupB->id]);

        // 4. Act: El admin del Grupo A intenta ver el documento del Grupo B
        // Si la Policy funciona bien, el method before() del Admin debería dejarlo pasar.
        $response = $this->actingAs($admin)->getJson("/api/documents/{$documentOfGroupB->id}");

        // 5. Assert: Debe ser 200 OK (Bypass exitoso)
        $response->assertStatus(200);
        $this->assertEquals($documentOfGroupB->id, $response->json('data.document.id'));
    }

    /** @test */
    public function a_regular_operator_cannot_access_documents_from_other_groups()
    {
        // 1. Setup: Grupos
        $area = Area::factory()->create();
        $gt = GroupType::factory()->create();
        $agt = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $gt->id]);
        $groupA = Group::factory()->create(['area_group_type_id' => $agt->id]);
        $groupB = Group::factory()->create(['area_group_type_id' => $agt->id]);

        // 2. Setup: Operador en Grupo A
        $operatorRole = Role::firstOrCreate(['name' => 'OPERADOR', 'guard_name' => 'web']);
        $operator = User::factory()->create(['group_id' => $groupA->id]);
        $operator->assignRole($operatorRole);

        // 3. Setup: Documento en Grupo B
        $documentOfGroupB = Document::factory()->create(['group_id' => $groupB->id]);

        // 4. Act: El operador intenta acceder (Debe fallar por Policy)
        $response = $this->actingAs($operator)->getJson("/api/documents/{$documentOfGroupB->id}");

        // 5. Assert: Debe ser 403 Forbidden (Seguridad activa)
        $response->assertStatus(403);
    }
}
