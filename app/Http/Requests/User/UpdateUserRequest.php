<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.update') ?? false;
    }

    public function rules(): array
    {
        $targetUser = $this->route('user');
        $selectedRoles = collect(
            $this->input('roles', $targetUser?->roles?->pluck('name')->all() ?? [])
        )->map(fn($role) => mb_strtoupper((string) $role));
        $isAdmin = $selectedRoles->contains('ADMINISTRADOR');

        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->user)],
            'dni' => ['required', 'string', 'max:10', Rule::unique('users')->ignore($this->user)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user)],
            'password' => 'nullable|string',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'group' => [Rule::requiredIf(!$isAdmin), 'nullable', 'exists:groups,id'],
            'subgroup' => 'nullable|exists:subgroups,id',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ];
    }
}
