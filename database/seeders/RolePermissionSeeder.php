<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'clear-system',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',

            'documents.view',
            'documents.create',
            'documents.update',
            'documents.delete',
            'documents.upload',
            'blocks.view',
            'blocks.create',
            'blocks.update',
            'blocks.delete',
            'blocks.upload',

            'areas.view',
            'areas.create',
            'areas.update',
            'areas.delete',
            'group-types.view',
            'group-types.create',
            'group-types.update',
            'group-types.delete',
            'groups.view',
            'groups.create',
            'groups.update',
            'groups.delete',
            'subgroups.view',
            'subgroups.create',
            'subgroups.update',
            'subgroups.delete',

            'document-types.view',
            'document-types.create',
            'document-types.update',
            'document-types.delete',
            'campos.view',
            'campos.create',
            'campos.update',
            'campos.delete',

            'sections.view',
            'sections.create',
            'sections.update',
            'sections.delete',
            'andamios.view',
            'andamios.create',
            'andamios.update',
            'andamios.delete',
            'boxes.view',
            'boxes.create',
            'boxes.update',
            'boxes.delete',

            'activity-logs.view',

            'inbox.view',

            'notifications.view',
            'notifications.receive',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR']);
        $adminRole->syncPermissions($permissions);
    }
}
