<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.create') ?? false;
    }

    public function rules(): array
    {
        $selectedRoles = collect($this->input('roles', []))->map(fn($role) => mb_strtoupper((string) $role));
        $isAdmin = $selectedRoles->contains('ADMINISTRADOR');

        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:users',
            'dni' => 'required|string|max:20|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:6',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'group_id' => [Rule::requiredIf(!$isAdmin), 'nullable', 'exists:groups,id'],
            'subgroup_id' => 'nullable|exists:subgroups,id',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ];
    }
}
