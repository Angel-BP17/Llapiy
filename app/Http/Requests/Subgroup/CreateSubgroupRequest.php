<?php

namespace App\Http\Requests\Subgroup;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubgroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('subgroups.create') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'group_id' => 'required|exists:groups,id',
            'descripcion' => 'nullable|string|max:255',
            'abreviacion' => 'nullable|string|max:255',
            'parent_subgroup_id' => 'nullable|exists:subgroups,id',
        ];
    }
}
