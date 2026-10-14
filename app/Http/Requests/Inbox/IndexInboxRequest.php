<?php

namespace App\Http\Requests\Inbox;

use Illuminate\Foundation\Http\FormRequest;

class IndexInboxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
            'area_id' => 'nullable|integer',
            'periodo' => 'nullable|integer',
            'fecha' => 'nullable|integer',
        ];
    }
}
