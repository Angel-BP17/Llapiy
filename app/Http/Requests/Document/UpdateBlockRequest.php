<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('blocks.update') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $canUploadFile = $this->user()?->can('blocks.upload') ?? false;

        return [
            'n_bloque' => [
                'required',
                'string',
                Rule::unique('blocks')->where(function ($query) {
                    return $query->where('periodo', $this->periodo);
                })->ignore($this->bloque)
            ],
            'fecha' => 'required|date',
            'asunto' => 'required|string|max:255',
            'folios' => 'required|string|max:255',
            'root' => $canUploadFile
                ? 'nullable|file|mimes:pdf|max:' . (50 * 1024)
                : 'prohibited',
            'rango_inicial' => 'required|integer',
            'rango_final' => 'required|integer',
        ];
    }
}
