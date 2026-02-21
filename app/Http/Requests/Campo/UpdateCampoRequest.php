<?php

namespace App\Http\Requests\Campo;

use App\Models\CampoType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('campos.update') ?? false;
    }

    public function rules(): array
    {
        $campo = $this->route('campo');
        $campoId = $campo instanceof CampoType ? $campo->id : $campo;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('campo_types', 'name')->ignore($campoId),
            ],
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

