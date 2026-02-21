<?php

namespace App\Http\Requests\Inbox;

use Illuminate\Foundation\Http\FormRequest;

class IndexInboxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('inbox.view') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
