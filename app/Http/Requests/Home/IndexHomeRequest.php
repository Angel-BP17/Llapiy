<?php

namespace App\Http\Requests\Home;

use Illuminate\Foundation\Http\FormRequest;

class IndexHomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAny([
            'users.view',
            'documents.view',
            'inbox.view',
            'document-types.view',
        ]) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
