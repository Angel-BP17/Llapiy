<?php

namespace App\Http\Requests\GroupType;

use Illuminate\Foundation\Http\FormRequest;

class IndexGroupTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
