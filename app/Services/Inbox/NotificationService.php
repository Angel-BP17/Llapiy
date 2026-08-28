<?php

namespace App\Services\Inbox;

use Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationService
{
    /**
     * Obtiene las notificaciones paginadas del usuario autenticado.
     */
    public function getNotifications(int $perPage = 10)
    {
        return Auth::user()
            ->notifications()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findNotificationOrFail(string $notificationId): DatabaseNotification
    {
        return Auth::user()->notifications()->findOrFail($notificationId);
    }

    public function isNotificationOwner($notification): bool
    {
        return (int)$notification->notifiable_id === (int)Auth::id();
    }
}
