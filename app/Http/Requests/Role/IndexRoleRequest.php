<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class IndexRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('roles.view') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
