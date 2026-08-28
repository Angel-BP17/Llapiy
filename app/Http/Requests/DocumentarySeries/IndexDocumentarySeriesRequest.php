<?php

namespace App\Http\Requests\DocumentarySeries;

use Illuminate\Foundation\Http\FormRequest;

class IndexDocumentarySeriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'nullable|string',
            'nombre' => 'nullable|string',
        ];
    }
}
