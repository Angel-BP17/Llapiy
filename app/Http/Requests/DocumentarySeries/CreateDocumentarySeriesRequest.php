<?php

namespace App\Http\Requests\DocumentarySeries;

use Illuminate\Foundation\Http\FormRequest;

class CreateDocumentarySeriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:50|unique:documentary_series,codigo',
            'nombre' => 'required|string|max:255',
        ];
    }
}
