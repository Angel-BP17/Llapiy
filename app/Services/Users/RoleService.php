<?php

namespace App\Services\Users;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function getIndexData(Request $request): array
    {
        $roles = Role::query()
            ->with('permissions')
            ->when($request->search, fn($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        return compact('roles', 'totalRoles', 'totalPermissions');
    }

    public function getPermissionGroups(): array
    {
        $permissions = Permission::orderBy('name')->get()->pluck('name')->all();
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

        $permissions = Permission::orderBy('name')->get()->pluck('name')->all();
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
        $role = Role::create(['name' => $name]);
        $role->syncPermissions($permissions);

        return $role;
    }

    public function getSelectedPermissions(Role $role): array
    {
        return $role->permissions()->pluck('name')->all();
    }

    public function update(Role $role, string $name, array $permissions = []): void
    {
        $role->update(['name' => $name]);
        $role->syncPermissions($permissions);
    }

    public function updatePermissions(Role $role, array $permissions = []): void
    {
        $role->syncPermissions($permissions);
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}
