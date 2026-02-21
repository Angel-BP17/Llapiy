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
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $users = User::all();
        $modules = ActivityLog::selectRaw("DISTINCT REPLACE(model, 'App\\\\Models\\\\', '') AS model")->pluck('model');

        return compact('logs', 'users', 'modules');
    }

    public function getReportLogs(Request $request): Collection
    {
        $query = ActivityLog::with('user');
        $this->applyFilters($query, $request);

        $logs = $query->orderBy('created_at', 'desc')->get();

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
