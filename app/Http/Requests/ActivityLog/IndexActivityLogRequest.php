<?php

namespace App\Http\Requests\ActivityLog;

use Illuminate\Foundation\Http\FormRequest;

class IndexActivityLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('activity-logs.view') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
