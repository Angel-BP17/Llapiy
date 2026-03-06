<?php

namespace App\Http\Requests\Block;

use Illuminate\Foundation\Http\FormRequest;

class UploadBlockFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'root' => 'required|file|mimes:pdf|max:' . (50 * 1024),
        ];
    }
}
