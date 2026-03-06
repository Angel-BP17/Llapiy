<?php

namespace App\Http\Requests\Block;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlockRequest extends FormRequest
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
        $canUploadFile = $this->user()?->can('blocks.upload') ?? false;
        $periodo = $this->filled('fecha') ? \Carbon\Carbon::parse($this->fecha)->year : null;
        $blockId = $this->route('block')?->id ?? $this->block;

        return [
            'n_bloque' => [
                'required',
                'string',
                Rule::unique('blocks')->where(function ($query) use ($periodo) {
                    return $query->where('periodo', $periodo);
                })->ignore($blockId)
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
