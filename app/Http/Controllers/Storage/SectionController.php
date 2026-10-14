<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storage\IndexSectionRequest;
use App\Models\Section;
use App\Services\Storage\SectionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    public function __construct(protected SectionService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexSectionRequest $request): Response
    {
        $search = $request->input('search');
        $periodo = $request->filled('periodo') ? (int) $request->input('periodo') : null;
        $sections = $this->service->getAll($search, $periodo);
        $stats = $this->service->getStorageStats();

        $searchedBlocks = $this->service->getSearchedBlocks($search, $periodo);

        return Inertia::render('storage/index', [
            'sections' => $sections->items(),
            'searchedBlocks' => $searchedBlocks,
            'stats' => $stats,
            'pagination' => [
                'total' => $sections->total(),
                'current_page' => $sections->currentPage(),
                'last_page' => $sections->lastPage(),
                'from' => $sections->firstItem(),
                'to' => $sections->lastItem(),
            ],
            'level' => 'sections',
            'filters' => [
                'search' => $search ?? '',
                'periodo' => $periodo ? (string) $periodo : '',
            ],
            'years' => \App\Models\Block::getAvailableYears(),
        ]);
    }

    /**
     * Generate storage report PDF.
     */
    public function report(Request $request)
    {
        try {
            $data = $this->service->getReportData();

            return Pdf::loadView('storage.report', $data)
                ->setPaper('a4', 'portrait')
                ->stream('reporte_almacenamiento.pdf');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error al generar reporte de almacenamiento: '.$e->getMessage());

            return response()->json(['message' => 'No se pudo generar el reporte de almacenamiento.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'n_section' => 'required|string|unique:sections,n_section',
            'descripcion' => 'required|string|max:255',
        ]);

        $section = $this->service->create($validated);

        return response()->json(['message' => 'Sección creada correctamente.', 'section' => $section], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section): Response
    {
        // For consistency with other modules, though currently it just returns inertia view
        return Inertia::render('storage/index', [
            'activeSection' => $section,
            'level' => 'sections',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Section $section): JsonResponse
    {
        $validated = $request->validate([
            'n_section' => 'required|string|unique:sections,n_section,'.$section->id,
            'descripcion' => 'required|string|max:255',
        ]);

        $this->service->update($section, $validated);

        return response()->json(['message' => 'Sección actualizada correctamente.', 'section' => $section->fresh()]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section): JsonResponse
    {
        $this->service->delete($section);

        return response()->json(['message' => 'Sección eliminada correctamente.']);
    }
}
