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
        $canUploadFile = $this->user()?->hasRole(['ARCHIVO_CENTRAL', 'ADMINISTRADOR']) ?? false;

        return [
            'n_bloque' => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('blocks')->where(function ($query) {
                    return $query->where('periodo', Carbon::parse($this->fecha)->year);
                }),
            ],
            'fecha' => 'required|date',
            'asunto' => 'required|string|max:255',
            'folios' => 'required|string|max:255',
            'root' => $canUploadFile
                ? 'nullable|file|mimes:pdf|max:'.(50 * 1024)
                : 'prohibited',
            'documentary_series_id' => 'nullable|exists:documentary_series,id',
            'periods' => 'required|array|min:1',
            'periods.*.rango_inicial' => 'required|integer|min:1',
            'periods.*.rango_final' => 'required|integer|gte:periods.*.rango_inicial',
            'periods.*.periodo' => 'required|integer|min:1900|max:'.(now()->year + 5),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'root.prohibited' => 'Solo los usuarios con el rol de Archivo Central o Administrador pueden subir el archivo PDF del bloque.',
            'root.mimes' => 'El archivo adjunto debe ser de formato PDF.',
            'root.max' => 'El archivo PDF no debe exceder los 50MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'n_bloque' => 'número de bloque',
        ];
    }
}
