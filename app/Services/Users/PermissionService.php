<?php

namespace App\Services\Users;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    /**
     * Crea un nuevo permiso en el sistema.
     */
    public function create(string $name): Permission
    {
        return Permission::create([
            'name' => $name,
            'guard_name' => $this->preferredGuardName(),
        ]);
    }

    public function update(Permission $permission, string $name): Permission
    {
        $permission->update(['name' => $name]);
        return $permission->fresh();
    }

    public function delete(Permission $permission): void
    {
        $permission->delete();
    }

    /**
     * Resuelve el guard predeterminado del sistema para asegurar consistencia.
     */
    protected function preferredGuardName(): string
    {
        return Permission::query()->value('guard_name')
            ?? Role::query()->value('guard_name')
            ?? config('auth.defaults.guard', 'web');
    }
}
