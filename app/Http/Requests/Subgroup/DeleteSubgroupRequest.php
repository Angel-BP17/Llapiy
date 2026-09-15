<?php

namespace App\Http\Requests\Subgroup;

use Illuminate\Foundation\Http\FormRequest;

class DeleteSubgroupRequest extends FormRequest
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
