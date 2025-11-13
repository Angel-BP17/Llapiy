<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->user)],
            'dni' => ['required', 'string', 'max:10', Rule::unique('users')->ignore($this->user)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user)],
            'password' => 'nullable|string',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_type_id' => 'required|exists:user_types,id',
            'group' => 'required|exists:groups,id',
            'subgroup' => 'nullable|exists:subgroups,id',
        ];
    }
}
