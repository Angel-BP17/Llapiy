<?php

namespace App\Http\Requests\Document;

use App\Models\CampoType;
use App\Models\DocumentType;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDocumentRequest extends FormRequest
{
    use HasDocumentValidation;

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
        $periodo = null;
        if ($this->filled('fecha')) {
            try {
                $periodo = Carbon::parse($this->input('fecha'))->year;
            } catch (\Throwable $e) {
                $periodo = null;
            }
        }

        return [
            'n_documento' => [
                'required',
                'string',
                'max:255',
                Rule::unique('documents')->where(function ($query) use ($periodo) {
                    return $query->where('periodo', $periodo);
                })
            ],
            'asunto' => 'required|string|max:1000',
            'root' => 'nullable|file|mimes:pdf|max:' . (15 * 1024),
            'folios' => 'required|string|max:50',
            'document_type_id' => 'required|exists:document_types,id',
            'fecha' => 'required|date',
            'campos' => 'sometimes|array',
            'campos.*.id' => 'required|integer|exists:campo_types,id',
            'campos.*.dato' => 'nullable',
        ];
    }

    protected function getDocumentTypeId(): int
    {
        return (int) $this->input('document_type_id');
    }
}

