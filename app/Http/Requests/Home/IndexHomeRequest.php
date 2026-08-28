<?php

namespace App\Http\Requests\Home;

use Illuminate\Foundation\Http\FormRequest;

class IndexHomeRequest extends FormRequest
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
