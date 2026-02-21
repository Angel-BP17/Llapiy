<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('documents.upload') ?? false;
    }

    public function rules(): array
    {
        return [
            'root' => 'required|file|mimes:pdf|max:' . (15 * 1024),
        ];
    }
}

