<?php

namespace App\Services\ActivityLog;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ActivityLogService
{
    public function getIndexData(Request $request): array
    {
        $query = ActivityLog::query();
        $this->applyFilters($query, $request);

        $logs = $query
            ->with('user:id,name,last_name')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Optimización: Cargar solo usuarios que tienen logs registrados para reducir el volumen de datos
        $users = \Illuminate\Support\Facades\Cache::remember('activity_users_list', now()->addMinutes(60), function() {
            return User::whereHas('activityLogs')
                ->select('id', 'name', 'last_name')
                ->orderBy('name')
                ->get();
        });

        // Optimización: Obtener modelos únicos y procesarlos en PHP para evitar REPLACE en SQL sobre tablas grandes
        $modules = \Illuminate\Support\Facades\Cache::remember('activity_modules_list', now()->addHours(12), function() {
            return ActivityLog::select('model')
                ->distinct()
                ->pluck('model')
                ->map(fn($model) => str_replace('App\\Models\\', '', $model))
                ->unique()
                ->values();
        });

        return compact('logs', 'users', 'modules');
    }

    public function getReportLogs(Request $request): Collection
    {
        $query = ActivityLog::with('user:id,name,last_name');
        $this->applyFilters($query, $request);

        // Prevenir Memory Exhaustion limitando el reporte a los últimos 5000 registros si no hay filtros estrictos
        $logs = $query->orderBy('created_at', 'desc')->take(5000)->get();

        foreach ($logs as $log) {
            $log->before = $this->formatJsonData($log->before);
            $log->after = $this->formatJsonData($log->after);
        }

        return $logs;
    }

    protected function applyFilters($query, Request $request): void
    {
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $query->where('model', 'like', '%' . $request->module . '%');
        }
    }

    protected function formatJsonData($jsonData)
    {
        if (is_array($jsonData)) {
            return $jsonData;
        }

        if (!$jsonData || !is_string($jsonData)) {
            return '-';
        }

        $decoded = json_decode($jsonData, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : '-';
    }
}
