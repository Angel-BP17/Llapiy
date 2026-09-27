<?php

namespace App\Http\Requests\Block;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBlockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'n_bloque' => [
                'nullable',
                'integer',
                Rule::unique('blocks')->where(function ($query) {
                    return $query->where('periodo', Carbon::parse($this->fecha)->year);
                }),
            ],
            'fecha' => 'required|date',
            'asunto' => 'required|string|max:255',
            'folios' => 'required|string|max:255',
            'root' => 'nullable|file|mimes:pdf|max:'.(50 * 1024),
            'rango_inicial' => 'required|integer',
            'rango_final' => 'required|integer',
            'documentary_series_id' => 'nullable|exists:documentary_series,id',
            'periods' => 'nullable|array|min:1',
            'periods.*.rango_inicial' => 'required_with:periods|integer|min:1',
            'periods.*.rango_final' => 'required_with:periods|integer|gte:periods.*.rango_inicial',
            'periods.*.periodo' => 'required_with:periods|integer|min:1900|max:'.(now()->year + 5),
        ];
    }
}
