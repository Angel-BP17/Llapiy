<?php

namespace App\Http\Requests\Storage;

use Illuminate\Foundation\Http\FormRequest;

class IndexAndamioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('andamios.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
        ];
    }
}
