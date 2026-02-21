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
    public function getDashboardData(): array
    {
        $userCount = User::count();
        $documentCount = Document::count() + Block::count();
        $documentTypeCount = DocumentType::count();
        $totalNoAlmacenados = Block::whereNull('box_id')->count();

        $documentosRecientes = Document::selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad')
            ->where('created_at', '>=', now()->subDays(17))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha', 'asc')
            ->get();

        $documentosPorTipoRaw = Document::join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->select('document_types.name as tipo', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('document_types.id', 'document_types.name')
            ->get();

        $total = $documentosPorTipoRaw->sum('cantidad');

        $documentosPorTipo = $documentosPorTipoRaw->map(function ($doc) use ($total) {
            return [
                'tipo' => $doc->tipo ?? 'Sin Tipo',
                'porcentaje' => round(($doc->cantidad / max($total, 1)) * 100, 2),
            ];
        });

        // 🔥 Parte compatible con MySQL y PostgreSQL
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $documentosPorMes = Document::selectRaw("
        DATE_TRUNC('month', created_at) as fecha,
        COUNT(*) as cantidad
    ")
                ->groupBy(DB::raw("DATE_TRUNC('month', created_at)"))
                ->orderBy('fecha')
                ->get();
        } else {
            $documentosPorMes = Document::selectRaw("
        DATE_FORMAT(created_at, '%Y-%m-01') as fecha,
        COUNT(*) as cantidad
    ")
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-01')"))
                ->orderBy('fecha')
                ->get();
        }

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

    public function seedDefaults(): void
    {
        Artisan::call('db:seed', ['--force' => true]);
    }
}

