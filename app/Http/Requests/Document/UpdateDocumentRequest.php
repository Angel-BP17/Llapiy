<?php

namespace App\Http\Requests\Document;

use App\Models\CampoType;
use App\Models\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentRequest extends FormRequest
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
        $documentId = $this->route('document')?->id ?? $this->document;
        $canUploadFile = $this->user()?->can('documents.upload') ?? false;

        return [
            'n_documento' => [
                'required',
                'string',
                'max:255',
                Rule::unique('documents', 'n_documento')->ignore($documentId)
            ],
            'asunto' => 'required|string|max:255',
            'root' => $canUploadFile
                ? 'nullable|file|mimes:pdf|max:' . (15 * 1024)
                : 'prohibited',
            'folios' => 'nullable|string|max:255',
            'fecha' => 'required|date',
            'campos' => 'sometimes|array',
            'campos.*.id' => 'required|integer|exists:campo_types,id',
            'campos.*.dato' => 'nullable',
        ];
    }

    protected function getDocumentTypeId(): int
    {
        return (int) ($this->route('document')?->document_type_id ?? $this->document_type_id ?? 0);
    }
}

