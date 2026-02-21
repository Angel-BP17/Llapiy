<?php

namespace App\Http\Controllers\Inbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\IndexNotificationRequest;
use App\Services\Inbox\NotificationService;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $service)
    {
    }

    public function index(IndexNotificationRequest $request)
    {
        return $this->apiSuccess('Notificaciones obtenidas correctamente.', [
            'notifications' => $this->service->getNotifications(),
        ]);
    }

    public function redirectAndMarkAsRead($notificationId)
    {
        $notification = $this->service->findNotificationOrFail($notificationId);

        if (!$this->service->isNotificationOwner($notification)) {
            return $this->apiError('Acceso no autorizado.', 403);
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return $this->apiSuccess('Notificacion marcada como leida.', [
            'notification' => $notification,
        ]);
    }
}
