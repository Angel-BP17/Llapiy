<?php

namespace App\Http\Requests\DocumentType;

use Illuminate\Foundation\Http\FormRequest;

class IndexDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('document-types.view') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
