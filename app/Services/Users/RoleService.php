<?php

namespace App\Services\Users;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * Obtiene el listado de roles con sus permisos asociados.
     */
    public function getIndexData(Request $request): array
    {
        $guardName = $this->preferredGuardName();

        $roles = Role::query()
            ->where('guard_name', $guardName)
            ->with('permissions:id,name')
            ->when($request->search, fn($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totalRoles = Role::where('guard_name', $guardName)->count();
        $totalPermissions = Permission::where('guard_name', $guardName)->count();

        return compact('roles', 'totalRoles', 'totalPermissions');
    }

    /**
     * Agrupa los permisos por módulos para facilitar la visualización en el frontend.
     */
    public function getPermissionGroups(): array
    {
        $permissions = Permission::where('guard_name', $this->preferredGuardName())
            ->orderBy('name')
            ->pluck('name');
            
        $labels = $this->getPermissionLabels();

        return $permissions->groupBy(function ($name) {
            return str_contains($name, '.') ? explode('.', $name)[0] : $name;
        })->map(function ($items, $key) use ($labels) {
            $main = $items->first(fn($n) => !str_contains($name = $n, '.') || str_ends_with($name, '.view'));
            return [
                'key' => $key,
                'module' => $main,
                'permissions' => $items->filter(fn($n) => $n !== $main)
                    ->sortBy(fn($n) => $labels[$n] ?? $n)
                    ->values()
            ];
        })->values()->all();
    }

    /**
     * Genera etiquetas legibles para los nombres técnicos de los permisos.
     */
    public function getPermissionLabels(): array
    {
        $actionLabels = ['view' => 'Ver', 'create' => 'Crear', 'update' => 'Actualizar', 'delete' => 'Eliminar'];
        $moduleLabels = $this->getModuleLabels();

        $customLabels = [
            'clear-system' => 'Limpiar sistema',
            'documents.upload' => 'Subir archivo de documentos',
            'blocks.upload' => 'Subir archivo de bloques',
            'notifications.receive' => 'Recibir notificaciones',
        ];

        return Permission::pluck('name')->mapWithKeys(function ($name) use ($actionLabels, $moduleLabels, $customLabels) {
            if (isset($customLabels[$name])) return [$name => $customLabels[$name]];
            
            if (str_contains($name, '.')) {
                [$mod, $act] = explode('.', $name, 2);
                if (isset($actionLabels[$act])) {
                    $modLabel = $moduleLabels[$mod] ?? ucfirst(str_replace('-', ' ', $mod));
                    return [$name => "{$actionLabels[$act]} " . strtolower($modLabel)];
                }
            }
            return [$name => $name];
        })->all();
    }

    protected function getModuleLabels(): array
    {
        return [
            'users' => 'Usuarios', 'roles' => 'Roles', 'permissions' => 'Permisos',
            'documents' => 'Documentos', 'blocks' => 'Bloques', 'areas' => 'Áreas',
            'group-types' => 'Tipos de grupos', 'groups' => 'Grupos', 'subgroups' => 'Subgrupos',
            'document-types' => 'Tipos de documentos', 'campos' => 'Campos', 'sections' => 'Secciones',
            'andamios' => 'Andamios', 'boxes' => 'Cajas', 'activity-logs' => 'Registro de actividades',
            'inbox' => 'Bandeja', 'notifications' => 'Notificaciones',
        ];
    }

    public function create(string $name, array $permissions = []): Role
    {
        return DB::transaction(function () use ($name, $permissions) {
            $guard = $this->resolveGuardName($permissions);
            $role = Role::create(['name' => $name, 'guard_name' => $guard]);
            $this->syncPermissionsByGuard($role, $permissions, $guard);
            return $role;
        });
    }

    public function update(Role $role, string $name, array $permissions = []): Role
    {
        return DB::transaction(function () use ($role, $name, $permissions) {
            $guard = $this->resolveGuardName($permissions, $role->guard_name);
            $role->update(['name' => $name, 'guard_name' => $guard]);
            $this->syncPermissionsByGuard($role, $permissions, $guard);
            return $role->fresh('permissions');
        });
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }

    protected function preferredGuardName(): string
    {
        return Permission::query()->value('guard_name') ?? config('auth.defaults.guard', 'web');
    }

    protected function resolveGuardName(array $permissions = [], ?string $fallback = null): string
    {
        $guards = Permission::whereIn('name', array_filter($permissions))->pluck('guard_name');
        return $guards->isEmpty() ? ($fallback ?: $this->preferredGuardName()) : $guards->countBy()->sortDesc()->keys()->first();
    }

    protected function syncPermissionsByGuard(Role $role, array $permissions, string $guardName): void
    {
        $permissionModels = Permission::where('guard_name', $guardName)->whereIn('name', array_filter($permissions))->get();
        if ($permissionModels->count() !== count(array_filter(array_unique($permissions)))) {
            throw ValidationException::withMessages(['permissions' => ['Permisos no válidos para el guard.']]);
        }
        $role->syncPermissions($permissionModels);
    }
}
