<?php

namespace App\Services\Home;

use App\Models\Block;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Artisan;

class HomeService
{
    /**
     * Obtiene todos los datos necesarios para el Dashboard.
     * Implementa optimizaciones de consulta y segmentación por roles.
     */
    public function getDashboardData(): array
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('ADMINISTRADOR');

        // 1. CONSULTAS BASE (Para reutilización y limpieza)
        $docBaseQuery = Document::query()->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id));
        $blockBaseQuery = Block::query()->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id));

        // 2. RESUMEN DE CONTADORES
        $userCount = $isAdmin ? User::count() : 0;
        $documentCount = $docBaseQuery->count() + $blockBaseQuery->count();
        $totalNoAlmacenados = (clone $blockBaseQuery)->whereNull('box_id')->count();

        // Lógica de tipos de documentos permitidos (coherente con DocumentService)
        $documentTypeCount = $isAdmin 
            ? DocumentType::count() 
            : DocumentType::whereHas('groups', fn($q) => $q->where('groups.id', $user->group_id))
                ->orWhereHas('subgroups', fn($q) => $q->where('subgroups.id', $user->subgroup_id))
                ->count();

        // 3. GRÁFICO: DOCUMENTOS RECIENTES (ÚLTIMOS 17 DÍAS)
        $documentosRecientes = (clone $docBaseQuery)
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad')
            ->where('created_at', '>=', now()->subDays(17))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha', 'asc')
            ->get();

        // 4. GRÁFICO: DISTRIBUCIÓN POR TIPO
        $documentosPorTipoRaw = (clone $docBaseQuery)
            ->join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->select('document_types.name as tipo', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('document_types.id', 'document_types.name')
            ->get();

        $totalDocs = $documentosPorTipoRaw->sum('cantidad');
        $documentosPorTipo = $documentosPorTipoRaw->map(fn($doc) => [
            'tipo' => $doc->tipo ?? 'Sin Tipo',
            'porcentaje' => $totalDocs > 0 ? round(($doc->cantidad / $totalDocs) * 100, 2) : 0,
        ]);

        // 5. GRÁFICO: HISTÓRICO MENSUAL (Compatibilidad Multi-DB)
        $isPgsql = DB::connection()->getDriverName() === 'pgsql';
        $monthlyExpression = $isPgsql 
            ? "TO_CHAR(DATE_TRUNC('month', created_at), 'YYYY-MM-01')"
            : "DATE_FORMAT(created_at, '%Y-%m-01')";

        $documentosPorMes = (clone $docBaseQuery)
            ->selectRaw("{$monthlyExpression} as fecha, COUNT(*) as cantidad")
            ->groupBy(DB::raw($monthlyExpression))
            ->orderBy('fecha')
            ->get();

        return compact(
            'userCount',
            'documentCount',
            'documentTypeCount',
            'totalNoAlmacenados',
            'documentosRecientes',
            'documentosPorTipo',
            'documentosPorMes'
        );
    }

    /**
     * Restablece los datos predeterminados del sistema.
     */
    public function seedDefaults(): void
    {
        Artisan::call('db:seed', ['--force' => true]);
    }
}
