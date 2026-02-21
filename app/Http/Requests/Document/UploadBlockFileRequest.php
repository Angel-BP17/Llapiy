<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class UploadBlockFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('blocks.upload') ?? false;
    }

    public function rules(): array
    {
        return [
            'root' => 'required|file|mimes:pdf|max:' . (50 * 1024),
        ];
    }
}

