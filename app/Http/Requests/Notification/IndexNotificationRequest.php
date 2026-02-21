<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class IndexNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('notifications.view') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
