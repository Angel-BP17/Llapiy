<?php

namespace App\Http\Requests\Campo;

use App\Models\CampoType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('campos.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:campo_types,name',
            'data_type' => ['nullable', Rule::in(CampoType::dataTypes())],
            'is_nullable' => 'nullable|boolean',
            'length' => 'nullable|integer|min:1|max:65535',
            'allow_negative' => 'nullable|boolean',
            'allow_zero' => 'nullable|boolean',
            'enum_values' => [
                Rule::requiredIf(fn() => $this->input('data_type') === 'enum'),
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}

