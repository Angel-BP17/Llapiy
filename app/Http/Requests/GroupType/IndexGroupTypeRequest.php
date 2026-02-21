<?php

namespace App\Http\Requests\GroupType;

use Illuminate\Foundation\Http\FormRequest;

class IndexGroupTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('group-types.view') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
