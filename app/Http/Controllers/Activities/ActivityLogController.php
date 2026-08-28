<?php

namespace App\Http\Controllers\Activities;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityLog\IndexActivityLogRequest;
use App\Services\ActivityLog\ActivityLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogService $service)
    {
    }

    /**
     * Display a listing of the activity logs.
     */
    public function index(IndexActivityLogRequest $request): Response
    {
        $data = $this->service->getIndexData($request);
        $logs = $data['logs'];

        return Inertia::render('activity_logs/index', [
            'logs' => [
                'data' => $logs->items(),
                'total' => $logs->total(),
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'from' => $logs->firstItem(),
            ],
            'users' => $data['users'],
            'modules' => $data['modules'],
            'filters' => $request->only(['date', 'user_id', 'module']),
        ]);
    }

    /**
     * Generate PDF report of activity logs.
     */
    public function pdf(Request $request)
    {
        try {
            $logs = $this->service->getReportLogs($request);

            return Pdf::loadView('activity_logs.report', compact('logs'))
                ->setPaper('a4', 'landscape')
                ->stream('Reporte_Actividades.pdf');
        } catch (\Throwable) {
            return response()->json(['message' => 'No se pudo generar el reporte de actividades.'], 500);
        }
    }
}
