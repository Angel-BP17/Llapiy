<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityLog\IndexActivityLogRequest;
use App\Services\ActivityLog\ActivityLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogService $service)
    {
    }

    public function index(IndexActivityLogRequest $request)
    {
        return $this->apiSuccess('Bitacora obtenida correctamente.', $this->service->getIndexData($request));
    }

    public function generatePDF(Request $request)
    {
        $logs = $this->service->getReportLogs($request);

        $pdf = Pdf::loadView('activity_logs.report', compact('logs'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Reporte_Actividades.pdf');
    }
}
