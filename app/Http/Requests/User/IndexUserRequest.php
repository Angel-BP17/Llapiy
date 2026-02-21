<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class IndexUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.view') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
