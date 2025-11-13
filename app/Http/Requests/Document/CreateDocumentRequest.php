<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDocumentRequest extends FormRequest
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
            'n_documento' => [
                'required',
                'string',
                Rule::unique('documents')->where(function ($query) {
                    return $query->where('periodo', $this->periodo);
                })
            ],
            'asunto' => 'required|string',
            'folios' => 'required|string',
            'document_type_id' => 'required|exists:document_types,id',
            'fecha' => 'required|date',
            'root' => 'required|file|mimes:pdf|max:' . (15 * 1024),
            'campos' => 'sometimes|array',
            'campos.*.id' => 'required|integer|exists:campo_types,id',
            'campos.*.dato' => 'nullable|string',
        ];
    }
}
