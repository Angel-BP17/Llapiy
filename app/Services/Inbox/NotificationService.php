<?php

namespace App\Services\Inbox;

use Auth;

class NotificationService
{
    public function getNotifications(int $perPage = 10)
    {
        return Auth::user()
            ->notifications()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findNotificationOrFail(string $notificationId)
    {
        return Auth::user()->notifications()->findOrFail($notificationId);
    }

    public function isNotificationOwner($notification): bool
    {
        return $notification->notifiable_id === Auth::id();
    }
}
