<?php

namespace App\Http\Controllers\Inbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\IndexNotificationRequest;
use App\Services\Inbox\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $service)
    {
    }

    /**
     * Display a listing of the notifications.
     */
    public function index(IndexNotificationRequest $request): JsonResponse
    {
        return response()->json([
            'notifications' => $this->service->getNotifications()->items(),
        ]);
    }

    /**
     * Mark the specified notification as read.
     */
    public function read($notificationId): JsonResponse
    {
        $notification = $this->service->findNotificationOrFail($notificationId);

        if (!$this->service->isNotificationOwner($notification)) {
            return response()->json(['message' => 'Acceso no autorizado.'], 403);
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return response()->json([
            'message' => 'Notificación marcada como leída.',
            'notification' => $notification,
        ]);
    }
}
