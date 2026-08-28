<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Area;
use App\Models\GroupType;
use App\Models\AreaGroupType;
use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class E2ETestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asegurar Rol y Permisos
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        $operatorRole = Role::firstOrCreate(['name' => 'OPERADOR', 'guard_name' => 'web']);
        
        $permissions = [
            'users.view', 'users.create', 'users.update', 'users.delete',
            'areas.view', 'roles.view',
            'campos.view', 'campos.create', 'campos.update', 'campos.delete',
            'document-types.view', 'blocks.view', 'inbox.view', 'sections.view', 'activity-logs.view',
            'sections.create', 'sections.update', 'sections.delete',
            'areas.create', 'areas.update', 'areas.delete',
            'group-types.view', 'group-types.create', 'group-types.update', 'group-types.delete'
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }
        
        $adminRole->syncPermissions(Permission::all());

        // 2. Crear Estructura Base para que no falle la selección de combos
        $area = Area::firstOrCreate(['descripcion' => 'AREA TEST E2E'], ['abreviacion' => 'E2E']);
        $gt = GroupType::firstOrCreate(['descripcion' => 'TIPO TEST E2E'], ['abreviacion' => 'TTE']);
        $agt = AreaGroupType::firstOrCreate(['area_id' => $area->id, 'group_type_id' => $gt->id]);
        $group = Group::firstOrCreate(
            ['descripcion' => 'GRUPO TEST E2E', 'area_group_type_id' => $agt->id],
            ['abreviacion' => 'GTE']
        );

        // 3. Crear Usuario Admin E2E
        $user = User::updateOrCreate(
            ['user_name' => 'ADMIN'],
            [
                'name' => 'Admin',
                'last_name' => 'E2E',
                'email' => 'admin_e2e@test.com',
                'password' => Hash::make('password'),
                'dni' => '00000000',
                'group_id' => $group->id
            ]
        );

        $user->assignRole($adminRole);
    }
}
