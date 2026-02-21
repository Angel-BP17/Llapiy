<?php

namespace App\Http\Requests\Document;

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
        return $this->user()?->can('blocks.create') ?? false;
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
                'required',
                'string',
                Rule::unique('blocks')->where(function ($query) {
                    return $query->where('periodo', Carbon::parse($this->fecha)->year);
                })
            ],
            'fecha' => 'required|date',
            'asunto' => 'required|string|max:255',
            'folios' => 'required|string|max:255',
            'root' => 'nullable|file|mimes:pdf|max:' . (50 * 1024),
            'rango_inicial' => 'required|integer',
            'rango_final' => 'required|integer',
        ];
    }
}
