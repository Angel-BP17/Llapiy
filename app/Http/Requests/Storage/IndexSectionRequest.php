<?php

namespace App\Http\Requests\Storage;

use Illuminate\Foundation\Http\FormRequest;

class IndexSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sections.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
        ];
    }
}
