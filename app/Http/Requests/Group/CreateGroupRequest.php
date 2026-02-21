<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('groups.create') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'area_id' => 'required|exists:areas,id',
            'group_type_id' => 'required|exists:group_types,id',
            'descripcion' => 'nullable|string'
        ];
    }
}
