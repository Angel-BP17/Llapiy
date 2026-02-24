<?php

namespace App\Services\Users;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function getIndexData(Request $request): array
    {
        $guardName = $this->preferredGuardName();

        $roles = Role::query()
            ->where('guard_name', $guardName)
            ->with('permissions')
            ->when($request->search, fn($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totalRoles = Role::query()->where('guard_name', $guardName)->count();
        $totalPermissions = Permission::query()->where('guard_name', $guardName)->count();

        return compact('roles', 'totalRoles', 'totalPermissions');
    }

    public function getPermissionGroups(): array
    {
        $permissions = Permission::query()
            ->where('guard_name', $this->preferredGuardName())
            ->orderBy('name')
            ->pluck('name')
            ->all();
        $permissionLabels = $this->getPermissionLabels();

        $groups = [];

        foreach ($permissions as $permission) {
            if (str_contains($permission, '.')) {
                [$module, $action] = explode('.', $permission, 2);
                $moduleKey = $module;
            } else {
                $moduleKey = $permission;
                $action = null;
            }

            if (!isset($groups[$moduleKey])) {
                $groups[$moduleKey] = [
                    'module' => null,
                    'permissions' => [],
                ];
            }

            if ($action === 'view' || $action === null) {
                $groups[$moduleKey]['module'] = $permission;
                continue;
            }

            $groups[$moduleKey]['permissions'][] = $permission;
        }

        foreach ($groups as &$group) {
            $group['permissions'] = collect($group['permissions'])
                ->sortBy(fn($name) => $permissionLabels[$name] ?? $name)
                ->values()
                ->all();
        }

        return collect($groups)
            ->map(fn($value, $key) => array_merge(['key' => $key], $value))
            ->values()
            ->all();
    }

    public function getPermissionLabels(): array
    {
        $actionLabels = [
            'view' => 'Ver',
            'create' => 'Crear',
            'update' => 'Actualizar',
            'delete' => 'Eliminar',
        ];

        $moduleLabels = $this->getModuleLabels();

        $labels = [
            'clear-system' => 'Limpiar sistema',
            'documents.upload' => 'Subir archivo de documentos',
            'blocks.upload' => 'Subir archivo de bloques',
            'notifications.receive' => 'Recibir notificaciones',
        ];

        $permissions = Permission::query()
            ->where('guard_name', $this->preferredGuardName())
            ->orderBy('name')
            ->pluck('name')
            ->all();
        foreach ($permissions as $permission) {
            if (isset($labels[$permission])) {
                continue;
            }
            if (str_contains($permission, '.')) {
                [$module, $action] = explode('.', $permission, 2);
                if (isset($actionLabels[$action])) {
                    $moduleLabel = $moduleLabels[$module] ?? ucfirst(str_replace('-', ' ', $module));
                    $labels[$permission] = $actionLabels[$action] . ' ' . strtolower($moduleLabel);
                }
            }
        }

        return $labels;
    }

    protected function getModuleLabels(): array
    {
        return [
            'users' => 'Usuarios',
            'roles' => 'Roles',
            'permissions' => 'Permisos',
            'documents' => 'Documentos',
            'blocks' => 'Bloques',
            'areas' => 'Areas',
            'group-types' => 'Tipos de grupos',
            'groups' => 'Grupos',
            'subgroups' => 'Subgrupos',
            'document-types' => 'Tipos de documentos',
            'campos' => 'Campos',
            'sections' => 'Secciones',
            'andamios' => 'Andamios',
            'boxes' => 'Cajas',
            'activity-logs' => 'Registro de actividades',
            'inbox' => 'Bandeja',
            'notifications' => 'Notificaciones',
            'clear-system' => 'Configuracion',
        ];
    }

    public function create(string $name, array $permissions = []): Role
    {
        return DB::transaction(function () use ($name, $permissions) {
            $guardName = $this->resolveGuardName($permissions);
            $role = Role::create([
                'name' => $name,
                'guard_name' => $guardName,
            ]);

            $this->syncPermissionsByGuard($role, $permissions, $guardName);

            return $role;
        });
    }

    public function getSelectedPermissions(Role $role): array
    {
        return $role->permissions()->pluck('name')->all();
    }

    public function update(Role $role, string $name, array $permissions = []): void
    {
        DB::transaction(function () use ($role, $name, $permissions) {
            $guardName = $this->resolveGuardName($permissions, $role->guard_name ?: $this->preferredGuardName());

            $role->update([
                'name' => $name,
                'guard_name' => $guardName,
            ]);

            $this->syncPermissionsByGuard($role, $permissions, $guardName);
        });
    }

    public function updatePermissions(Role $role, array $permissions = []): void
    {
        DB::transaction(function () use ($role, $permissions) {
            $guardName = $this->resolveGuardName($permissions, $role->guard_name ?: $this->preferredGuardName());
            if ($role->guard_name !== $guardName) {
                $role->update(['guard_name' => $guardName]);
            }

            $this->syncPermissionsByGuard($role, $permissions, $guardName);
        });
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }

    protected function preferredGuardName(): string
    {
        return Permission::query()->value('guard_name')
            ?? Role::query()->value('guard_name')
            ?? config('auth.defaults.guard', 'web');
    }

    protected function resolveGuardName(array $permissions = [], ?string $fallback = null): string
    {
        $permissionNames = collect($permissions)->filter()->unique()->values();
        if ($permissionNames->isEmpty()) {
            return $fallback ?: $this->preferredGuardName();
        }

        $guards = Permission::query()
            ->whereIn('name', $permissionNames)
            ->pluck('guard_name')
            ->filter()
            ->values();

        if ($guards->isEmpty()) {
            return $fallback ?: $this->preferredGuardName();
        }

        $guardCount = $guards->countBy();
        if ($fallback !== null && $guardCount->has($fallback)) {
            return $fallback;
        }

        return (string) $guardCount->sortDesc()->keys()->first();
    }

    protected function syncPermissionsByGuard(Role $role, array $permissions, string $guardName): void
    {
        $permissionNames = collect($permissions)->filter()->unique()->values();
        if ($permissionNames->isEmpty()) {
            $role->syncPermissions([]);

            return;
        }

        $permissionModels = Permission::query()
            ->where('guard_name', $guardName)
            ->whereIn('name', $permissionNames)
            ->get();

        if ($permissionModels->count() !== $permissionNames->count()) {
            throw ValidationException::withMessages([
                'permissions' => ['Los permisos seleccionados no corresponden al guard esperado.'],
            ]);
        }

        $role->syncPermissions($permissionModels);
    }
}
