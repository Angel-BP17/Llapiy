<?php

namespace App\Http\Requests\Area;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAreaRequest extends FormRequest
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
