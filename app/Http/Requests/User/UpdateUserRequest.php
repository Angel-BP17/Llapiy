<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('role_id')) {
            $role = \Spatie\Permission\Models\Role::find($this->role_id);
            if ($role) {
                $this->merge([
                    'roles' => [$role->name]
                ]);
            }
        }

        if ($this->has('roles')) {
            $upperRoles = collect($this->input('roles', []))
                ->map(fn($role) => mb_strtoupper((string) $role))
                ->all();

            $this->merge([
                'roles' => $upperRoles
            ]);

            if (!$this->has('role_id') || empty($this->role_id)) {
                $roleName = collect($upperRoles)->first();
                $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
                if ($role) {
                    $this->merge([
                        'role_id' => $role->id
                    ]);
                }
            }
        }
    }

    public function rules(): array
    {
        $targetUser = $this->route('user');
        $userId = $targetUser instanceof User ? $targetUser->id : $targetUser;

        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($userId)],
            'dni' => ['required', 'string', 'max:10', Rule::unique('users')->ignore($userId)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'password' => 'nullable|string|min:6',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'group_id' => [
                Rule::requiredIf(fn() => $this->filled('area_id')),
                'nullable',
                'exists:groups,id'
            ],
            'subgroup_id' => 'nullable|exists:subgroups,id',
            'role_id' => 'required|exists:roles,id',
            'area_id' => 'nullable|exists:areas,id',
            'group_type_id' => 'nullable|exists:group_types,id',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ];
    }
}
