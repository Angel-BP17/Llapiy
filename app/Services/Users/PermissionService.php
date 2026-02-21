<?php

namespace App\Services\Users;

use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function create(string $name): void
    {
        Permission::create(['name' => $name]);
    }

    public function update(Permission $permission, string $name): void
    {
        $permission->update(['name' => $name]);
    }

    public function delete(Permission $permission): void
    {
        $permission->delete();
    }
}

