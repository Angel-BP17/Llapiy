<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Models\ActivityLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('admin');
            return $middleware->handle($request, $next);
        })->except('formatJsonData');
    }
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        // Filtro por Fecha
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filtro por Usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por Módulo
        if ($request->filled('module')) {
            $query->where('model', 'like', '%' . $request->module . '%');
        }

        // Obtener registros paginados
        $logs = $query->orderBy('created_at', 'desc')->paginate(10);

        // Obtener lista de usuarios y módulos para los filtros
        $users = User::all();
        $modules = ActivityLog::selectRaw("DISTINCT REPLACE(model, 'App\\\\Models\\\\', '') AS model")->pluck('model');

        return view('activity_logs.index', compact('logs', 'users', 'modules'));
    }

    public function generatePDF(Request $request)
    {
        // Consulta base con relaciones cargadas
        $query = ActivityLog::with('user');

        // Filtro por Fecha
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filtro por Usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por Módulo
        if ($request->filled('module')) {
            $query->where('model', 'like', '%' . $request->module . '%');
        }

        // Aplicar orden descendente y obtener los datos
        $logs = $query->orderBy('created_at', 'desc')->get();

        foreach ($logs as $log) {
            $log->before = $this->formatJsonData($log->before);
            $log->after = $this->formatJsonData($log->after);
        }

        // Generar el PDF
        $pdf = Pdf::loadView('activity_logs.report', compact('logs'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Reporte_Actividades.pdf');
    }

    private function formatJsonData($jsonData)
    {
        if (is_array($jsonData)) {
            return $jsonData; // Ya es un array, no necesita decodificación
        }

        if (!$jsonData || !is_string($jsonData)) {
            return '-'; // Si está vacío o no es una cadena, retorna "-"
        }

        $decoded = json_decode($jsonData, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : '-';
    }
}

