<?php

namespace App\Http\Controllers;

use App\Models\Areas\Area;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function getDashboardStats()
    {
        // Conteo de documentos por area
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
}
