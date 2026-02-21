<?php

namespace App\Http\Requests\Storage;

use Illuminate\Foundation\Http\FormRequest;

class IndexBoxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('boxes.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
        ];
    }
}
