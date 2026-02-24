<?php

namespace App\Services\Users;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    public function create(string $name): Permission
    {
        return Permission::create([
            'name' => $name,
            'guard_name' => $this->preferredGuardName(),
        ]);
    }

    public function update(Permission $permission, string $name): void
    {
        $permission->update(['name' => $name]);
    }

    public function delete(Permission $permission): void
    {
        $permission->delete();
    }

    protected function preferredGuardName(): string
    {
        return Permission::query()->value('guard_name')
            ?? Role::query()->value('guard_name')
            ?? config('auth.defaults.guard', 'web');
    }
}
