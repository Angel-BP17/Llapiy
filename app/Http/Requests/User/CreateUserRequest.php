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
                    'roles' => [$role->name],
                ]);
            }
        }

        if ($this->has('roles')) {
            $upperRoles = collect($this->input('roles', []))
                ->map(fn ($role) => mb_strtoupper((string) $role))
                ->all();

            $this->merge([
                'roles' => $upperRoles,
            ]);

            if (! $this->has('role_id') || empty($this->role_id)) {
                $roleName = collect($upperRoles)->first();
                $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
                if ($role) {
                    $this->merge([
                        'role_id' => $role->id,
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
            'user_name' => 'required|string|max:255|unique:users,user_name',
            'dni' => 'required|string|max:10|unique:users,dni',
            'email' => 'nullable|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:6',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->filled('password_confirmation') && $value !== $this->input('password_confirmation')) {
                        $fail('La confirmación de la contraseña no coincide.');
                    }
                },
            ],
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'group_id' => [
                Rule::requiredIf(fn () => $this->filled('area_id')),
                'nullable',
                'exists:groups,id',
            ],
            'subgroup_id' => 'nullable|exists:subgroups,id',
            'role_id' => 'required|exists:roles,id',
            'area_id' => 'nullable|exists:areas,id',
            'group_type_id' => 'nullable|exists:group_types,id',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'Los apellidos son obligatorios.',
            'user_name.required' => 'El nombre de usuario es obligatorio.',
            'user_name.unique' => 'Este nombre de usuario ya se encuentra registrado.',
            'dni.required' => 'El documento de identidad (DNI) es obligatorio.',
            'dni.max' => 'El DNI no debe exceder los 10 dígitos o caracteres.',
            'dni.unique' => 'Este DNI ya está registrado por otro usuario.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está registrado por otro colaborador.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'role_id.required' => 'Debe seleccionar un rol para el usuario.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'group_id.required' => 'Debe seleccionar un grupo cuando se asigna un área.',
            'foto_perfil.image' => 'El archivo adjunto debe ser una imagen válida (JPEG, PNG, JPG o GIF).',
            'foto_perfil.max' => 'La foto de perfil no debe superar los 2 MB de tamaño.',
        ];
    }
}
