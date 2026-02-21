<?php
namespace App\Services\User;

use App\Models\Area;
use App\Models\User;
use Hash;
use Storage;

class UserService
{
    public function getAll($data)
    {
        $users = User::with([
            'group.areaGroupType.area',
            'group.areaGroupType.groupType',
            'subgroup',
            'roles',
        ])->when($data->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('dni', 'LIKE', "%{$search}%")
                    ->orWhere('user_name', 'LIKE', "%{$search}%")
                    ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CONCAT(last_name, ' ', name) LIKE ?", ["%{$search}%"]);
            });
        })
            ->paginate(10);

        $areas = Area::with('areaGroupTypes.groupType', 'areaGroupTypes.groups.subgroups')->get();

        return ['users' => $users, 'areas' => $areas];
    }
    public function create(array $data, $fotoPerfil = null)
    {
        $dataValidated = $data;

        $roles = $dataValidated['roles'] ?? [];
        unset($dataValidated['roles']);

        $dataValidated['password'] = Hash::make($dataValidated['password']);
        $dataValidated['foto_perfil'] = $fotoPerfil?->store('usuarios/perfiles', 'public');

        $user = User::create($dataValidated);
        $user->syncRoles($roles);

        return $user;
    }

    public function update($data, $user)
    {
        $dataValidated = $data->validated();

        $roles = $dataValidated['roles'] ?? null;
        unset($dataValidated['roles']);

        if ($data->filled('password')) {
            $dataValidated['password'] = Hash::make($data['password']);
        } else {
            unset($dataValidated['password']);
        }

        if ($data->hasFile('foto_perfil')) {
            Storage::disk('public')->delete($user->foto_perfil);
            $dataValidated['foto_perfil'] = $data->file('foto_perfil')->store('usuarios/perfiles', 'public');
        }

        $dataValidated['group_id'] = $data['group'];
        unset($dataValidated['group']);

        $dataValidated['subgroup_id'] = $data['subgroup'];
        unset($dataValidated['subgroup']);

        $user->update($dataValidated);

        if (is_array($roles)) {
            $user->syncRoles($roles);
        }
    }

    public function delete($data)
    {
        if ($data->foto_perfil) {
            Storage::disk('public')->delete($data->foto_perfil);
        }

        $data->delete();
    }

    public function getUsersForDpf($data)
    {
        return User::when($data->name, fn($q, $name) => $q->where('name', 'like', "%{$name}%"))
            ->when($data->last_name, fn($q, $lastName) => $q->where('last_name', 'like', "%{$lastName}%"))
            ->when($data->role_id, fn($q, $roleId) => $q->where('role_id', $roleId))
            ->when($data->status, fn($q, $status) => $q->where('status', $status))
            ->when(
                $data->filled('from_date') && $data->filled('to_date'),
                fn($q) => $q->whereBetween('created_at', [$data->from_date, $data->to_date])
            )
            ->get();
    }
}
