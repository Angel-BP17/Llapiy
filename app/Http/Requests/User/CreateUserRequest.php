<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:users',
            'dni' => 'required|string|max:10|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:6',
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
