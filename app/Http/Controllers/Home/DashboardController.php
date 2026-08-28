<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\IndexHomeRequest;
use App\Services\Home\HomeService;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(protected HomeService $service)
    {
    }

    /**
     * Display the dashboard.
     */
    public function index(IndexHomeRequest $request): Response
    {
        $data = $this->service->getDashboardData();

        return Inertia::render('dashboard', [
            'stats' => [
                'userCount' => $data['userCount'] ?? 0,
                'documentCount' => $data['documentCount'] ?? 0,
                'totalNoAlmacenados' => $data['totalNoAlmacenados'] ?? 0,
                'documentTypeCount' => $data['documentTypeCount'] ?? 0,
            ],
            'documentosRecientes' => $data['documentosRecientes'] ?? [],
            'documentosPorTipo' => $data['documentosPorTipo'] ?? [],
            'docsByArea' => $data['docs_by_area'] ?? [],
            'funnelData' => $data['funnelData'] ?? [],
            'activityStats' => $data['activityStats'] ?? [],
        ]);
    }

    /**
     * Get dashboard stats.
     */
    public function stats(): JsonResponse
    {
        $docsByArea = DB::table('areas')
            ->leftJoin('area_group_types', 'areas.id', '=', 'area_group_types.area_id')
            ->leftJoin('groups', 'area_group_types.id', '=', 'groups.area_group_type_id')
            ->leftJoin('documents', 'groups.id', '=', 'documents.group_id')
            ->select('areas.descripcion as area', DB::raw('count(documents.id) as total'))
            ->groupBy('areas.id', 'areas.descripcion')
            ->get();

        return response()->json([
            'docs_by_area' => $docsByArea,
            'total_global' => Document::count()
        ]);
    }

    public function notifications(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([]);
        }

        return response()->json(
            $user->unreadNotifications()->latest()->take(10)->get()
        );
    }
}
