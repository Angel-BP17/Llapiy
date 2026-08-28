<?php

namespace App\Http\Requests\Block;

use Illuminate\Foundation\Http\FormRequest;

class IndexBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asunto' => 'nullable|string|max:255',
            'n_bloque' => 'nullable|string|max:255',
            'area_id' => 'nullable|integer|exists:areas,id',
            'group_id' => 'nullable|integer|exists:groups,id',
            'subgroup_id' => 'nullable|integer|exists:subgroups,id',
            'year' => 'nullable|integer',
            'month' => 'nullable|integer|min:1|max:12',
            'role_id' => 'nullable|integer|exists:roles,id',
            'section_id' => 'nullable|integer|exists:sections,id',
            'andamio_id' => 'nullable|integer|exists:andamios,id',
            'box_id' => 'nullable|integer|exists:boxes,id',
        ];
    }
}
