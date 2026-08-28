<?php

namespace App\Http\Requests\DocumentarySeries;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentarySeriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $series = $this->route('documentary_series');
        $seriesId = is_object($series) ? $series->id : $series;

        return [
            'codigo' => 'required|string|max:50|unique:documentary_series,codigo,' . $seriesId,
            'nombre' => 'required|string|max:255',
        ];
    }
}
