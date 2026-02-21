<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class IndexBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('blocks.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'asunto' => 'nullable|string|max:255',
            'area_id' => 'nullable|integer|exists:areas,id',
            'group_id' => 'nullable|integer|exists:groups,id',
            'subgroup_id' => 'nullable|integer|exists:subgroups,id',
            'year' => 'nullable|integer',
            'month' => 'nullable|integer|min:1|max:12',
            'role_id' => 'nullable|integer|exists:roles,id',
        ];
    }
}
