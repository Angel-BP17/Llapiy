<?php

namespace App\Http\Controllers\Inbox;

use Auth;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    /**
     * Muestra todas las notificaciones del usuario autenticado.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications;

        return view('notifications.index', compact('notifications'));
    }

    public function redirectAndMarkAsRead($notificationId)
    {
        // Buscar la notificación
        $notification = Auth::user()->notifications()->findOrFail($notificationId);

        // Verificar que la notificación pertenece al usuario autenticado
        if ($notification->notifiable_id !== Auth::id()) {
            return redirect()->route('index')->with('error', 'Acceso no autorizado.');
        }

        // Marcar la notificación como leída
        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        // Redirigir al documento relacionado (u otro destino configurado)
        return redirect()->route('inbox.index');
    }
}
