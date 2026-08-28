<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->logActivity('create', $model, null, $model->getAttributes());
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $after = $model->getChanges();

        // Evitar registrar si no hay cambios reales (excepto timestamps)
        unset($after['updated_at']);
        if (empty($after)) {
            return;
        }

        $this->logActivity('update', $model, $model->getOriginal(), $model->getChanges());
    }

    /**
     * Handle the Model "deleting" event.
     */
    public function deleting(Model $model): void
    {
        // Guardamos los datos antes de la eliminación física
        $model->beforeDeleteData = $model->getOriginal();
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $before = $model->beforeDeleteData ?? $model->getOriginal();

        if (empty($before)) {
            return;
        }

        $this->logActivity('delete', $model, $before, null);
    }

    /**
     * Core logic to save the activity log synchronously.
     */
    private function logActivity(string $action, Model $model, ?array $before = null, ?array $after = null): void
    {
        // Evitar bucles infinitos si se audita el propio log
        if ($model instanceof ActivityLog) {
            return;
        }

        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model' => get_class($model),
                'before' => $before ? json_encode($before) : null,
                'after' => $after ? json_encode($after) : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Fallo al guardar log de auditoría: " . $e->getMessage(), [
                'action' => $action,
                'model' => get_class($model)
            ]);
        }
    }
}
