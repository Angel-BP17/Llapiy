<?php

namespace App\Http\Requests\Campo;

use Illuminate\Foundation\Http\FormRequest;

class IndexCampoRequest extends FormRequest
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
