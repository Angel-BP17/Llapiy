<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AuthMiddlewareFactory;
use App\Models\{Document, DocumentType, User, Block, GroupDocumentType, SubgroupDocumentType};
use DB;
use Illuminate\Support\Facades\{Auth};

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            return AuthMiddlewareFactory::make('base')->handle($request, $next);
        })->except(['documentosRecientes', 'documentosPorTipo', 'documentosPorMes']);
    }
    public function index()
    {
        if (Auth::user()->userType->name !== 'Administrador' && Auth::user()->userType->name !== 'Revisor/Aprobador') {
            $documentCount = Document::where('user_id', Auth::user()->id)->count();
            $documentTypeCount = GroupDocumentType::where('group_id', Auth::user()->group_id)->count() + SubgroupDocumentType::where('subgroup_id', Auth::user()->subgroup_id)->count();

            $totalNoAlmacenados = Block::whereNull('box_id')->where('user_id', Auth::user()->id)->count();

            return view('dashboard', compact("documentCount", "totalNoAlmacenados", "documentTypeCount"));
        } else {
            $userCount = User::count();
            $documentCount = Document::count() + Block::count();
            $documentTypeCount = DocumentType::count();
            $totalNoAlmacenados = Block::whereNull('box_id')->count();

            // Datos para los gráficos:

            // Documentos recientes últimos 17 días (fecha y cantidad)
            $documentosRecientes = Document::selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad')
                ->where('created_at', '>=', now()->subDays(17))
                ->groupBy(DB::raw('DATE(created_at)'))  // corregido: agrupar por expresión
                ->orderBy('fecha', 'asc')
                ->get();

            // Porcentaje de documentos por tipo
            $documentosPorTipoRaw = Document::join('document_types', 'documents.document_type_id', '=', 'document_types.id')
                ->select('document_types.name as tipo', DB::raw('COUNT(*) as cantidad'))
                ->groupBy('document_types.id', 'document_types.name')
                ->get();

            $total = $documentosPorTipoRaw->sum('cantidad');

            $documentosPorTipo = $documentosPorTipoRaw->map(function ($doc) use ($total) {
                return [
                    'tipo' => $doc->tipo ?? 'Sin Tipo',
                    'porcentaje' => round(($doc->cantidad / max($total, 1)) * 100, 2) // evita división por cero
                ];
            });

            // Documentos por mes y año
            $documentosPorMes = Document::selectRaw('YEAR(created_at) as anio, MONTH(created_at) as mes, COUNT(*) as cantidad')
                ->groupBy('anio', 'mes')
                ->orderByRaw('anio ASC, mes ASC')
                ->get();

            return view('dashboard', compact(
                "userCount",
                "documentCount",
                "documentTypeCount",
                "totalNoAlmacenados",
                "documentosRecientes",
                "documentosPorTipo",
                "documentosPorMes"
            ));
        }
    }

    public function seedDefaults()
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);

            return redirect()->route('dashboard')->with('success', '✅ Datos predeterminados insertados correctamente.');

        } catch (\Throwable $e) {
            return redirect()->route('dashboard')->withErrors('❌ Error al ejecutar seeding: ' . $e->getMessage());
        }
    }

}