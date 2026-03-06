<?php
namespace App\Services\User;

use App\Models\Area;
use App\Models\User;
use Hash;
use Storage;
use DB;

class UserService
{
    /**
     * Obtiene el listado de usuarios con sus roles y ubicación organizacional.
     * Optimizado con Column Limiting y Caché para catálogos.
     */
    public function getAll($data): array
    {
        $users = User::with([
            'group:id,area_group_type_id,descripcion',
            'group.areaGroupType:id,area_id,group_type_id',
            'group.areaGroupType.area:id,descripcion',
            'group.areaGroupType.groupType:id,descripcion',
            'subgroup:id,descripcion',
            'roles:id,name',
        ])
        ->select(['id', 'name', 'last_name', 'dni', 'user_name', 'email', 'foto_perfil', 'group_id', 'subgroup_id', 'created_at'])
        ->when($data->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('dni', 'LIKE', "%{$search}%")
                    ->orWhere('user_name', 'LIKE', "%{$search}%")
                    ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        })
        ->paginate(10);

        $areas = \Illuminate\Support\Facades\Cache::remember('areas_groups_subgroups_tree', now()->addHours(12), function() {
            return Area::with('areaGroupTypes.groupType', 'areaGroupTypes.groups.subgroups')->get();
        });

        return compact('users', 'areas');
    }

    public function create(array $data, $fotoPerfil = null): User
    {
        return DB::transaction(function () use ($data, $fotoPerfil) {
            $roles = $data['roles'] ?? [];
            unset($data['roles']);

            $data['password'] = Hash::make($data['password']);
            if ($fotoPerfil) {
                $data['foto_perfil'] = $fotoPerfil->store('usuarios/perfiles', 'public');
            }

            $user = User::create($data);
            $user->syncRoles($roles);

            return $user;
        });
    }

    public function update($request, User $user): User
    {
        return DB::transaction(function () use ($request, $user) {
            $data = $request->validated();
            $roles = $data['roles'] ?? null;
            unset($data['roles']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            } else {
                unset($data['password']);
            }

            if ($request->hasFile('foto_perfil')) {
                if ($user->foto_perfil) {
                    Storage::disk('public')->delete($user->foto_perfil);
                }
                $data['foto_perfil'] = $request->file('foto_perfil')->store('usuarios/perfiles', 'public');
            }

            $user->update($data);

            if (is_array($roles)) {
                $user->syncRoles($roles);
            }

            return $user->fresh(['roles', 'group', 'subgroup']);
        });
    }

    public function delete(User $user): void
    {
        if ($user->foto_perfil) {
            Storage::disk('public')->delete($user->foto_perfil);
        }
        $user->delete();
    }
}
