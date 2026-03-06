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
        if ($this->has('roles')) {
            $this->merge([
                'roles' => collect($this->input('roles', []))
                    ->map(fn($role) => mb_strtoupper((string) $role))
                    ->all()
            ]);
        }
    }

    public function rules(): array
    {
        $targetUser = $this->route('user');
        $userId = $targetUser instanceof User ? $targetUser->id : $targetUser;

        $roles = collect($this->input('roles', []));
        $isAdmin = $roles->contains('ADMINISTRADOR');

        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($userId)],
            'dni' => ['required', 'string', 'max:10', Rule::unique('users')->ignore($userId)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'password' => 'nullable|string|min:6',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'group_id' => [Rule::requiredIf(!$isAdmin), 'nullable', 'exists:groups,id'],
            'subgroup_id' => 'nullable|exists:subgroups,id',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ];
    }
}
