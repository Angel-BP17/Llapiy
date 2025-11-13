<?php

namespace App\Observers;

use App\Jobs\LogActivityJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityObserver
{
    public function created(Model $model)
    {
        // Obtener todos los atributos del modelo recién creado
        $after = $model->getAttributes();

        $this->logActivity('create', $model, null, $after);
    }

    public function updated(Model $model)
    {
        // Capturar valores antes y después de la actualización
        $before = $model->getOriginal();
        $after = $model->getChanges();

        // Evitar registrar si no hay cambios
        if (empty($after)) {
            \Log::info('No se detectaron cambios en la actualización del modelo: ' . get_class($model));
            return;
        }

        $this->logActivity('update', $model, $before, $after);
    }

    public function deleting(Model $model)
    {
        // Guardar los datos antes de que se eliminen
        $model->beforeDeleteData = $model->getOriginal();
    }

    public function deleted(Model $model)
    {
        // Usar los datos guardados en deleting()
        $before = $model->beforeDeleteData ?? null;

        // Si no hay datos antes de eliminar, evitar registro vacío
        if (empty($before)) {
            \Log::warning('No se encontraron datos antes de eliminar el modelo: ' . get_class($model));
            return;
        }

        $this->logActivity('delete', $model, $before, null);
    }

    private function logActivity(string $action, Model $model, $before = null, $after = null)
    {
        if ($model instanceof \App\Models\ActivityLog) {
            return;
        }

        if ($action === 'update' && $before === null && $after === null) {
            $before = $model->getOriginal();
            $after = $model->getChanges();
        }

        \Log::info("Registro de actividad", [
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => get_class($model),
            'before' => $before,
            'after' => $after,
            'created_at' => now(),
        ]);

        LogActivityJob::dispatch([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => get_class($model),
            'before' => $before,
            'after' => $after,
            'created_at' => now(),
        ]);
    }
}
