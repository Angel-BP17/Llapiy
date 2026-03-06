<?php

namespace Tests\Feature\Controllers;

use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\Group;
use App\Models\GroupType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    public function test_profile_endpoint_returns_user_data_and_permissions(): void
    {
        // 1. Setup: Crear estructura organizacional
        $area = Area::create(['descripcion' => 'Area Test', 'abreviacion' => 'AT']);
        $groupType = GroupType::create(['descripcion' => 'Tipo Test', 'abreviacion' => 'TT']);
        $areaGroupType = AreaGroupType::create([
            'area_id' => $area->id,
            'group_type_id' => $groupType->id
        ]);
        $group = Group::create([
            'area_group_type_id' => $areaGroupType->id,
            'descripcion' => 'Grupo Test',
            'abreviacion' => 'GT'
        ]);

        // 2. Setup: Roles y Permisos
        $role = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        $permission1 = Permission::firstOrCreate(['name' => 'users.view', 'guard_name' => 'web']);
        $permission2 = Permission::firstOrCreate(['name' => 'documents.upload', 'guard_name' => 'web']);
        $role->givePermissionTo([$permission1, $permission2]);

        // 3. Setup: Usuario con relaciones
        $user = User::create([
            'name' => 'Angel',
            'last_name' => 'Test',
            'user_name' => 'angeltest',
            'email' => 'angel@test.com',
            'password' => bcrypt('password'),
            'dni' => '12345678',
            'group_id' => $group->id
        ]);
        $user->assignRole($role);

        // 4. Act: Llamar al endpoint
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/profile');

        // 5. Assert: Verificar estructura y datos siguiendo el NUEVO CONTRATO
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => [
                        'id', 'name', 'last_name', 'user_name', 'email', 'dni',
                        'area', 'grupo', 'subgrupo'
                    ],
                    'permissions',
                    'role_names'
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Angel',
                'area' => 'Area Test',
                'grupo' => 'Grupo Test',
                'subgrupo' => 'Sin Subgrupo'
            ]);

        // Verificar mapa de permisos (ahora es un objeto de booleanos)
        $permissions = $response->json('data.permissions');
        $this->assertTrue($permissions['users.view']);
        $this->assertTrue($permissions['documents.upload']);
        
        // Verificar nombres de roles
        $this->assertContains('ADMINISTRADOR', $response->json('data.role_names'));
    }
}
