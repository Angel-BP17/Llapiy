<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storage\IndexSectionRequest;
use App\Models\Section;
use App\Services\Storage\SectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    public function __construct(protected SectionService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexSectionRequest $request): Response
    {
        $search = $request->input('search');
        $sections = $this->service->getAll($search);

        $searchedBlocks = [];
        if ($search) {
            $searchedBlocks = \App\Models\Block::query()
                ->select(['id', 'n_bloque', 'asunto', 'folios', 'periodo', 'box_id'])
                ->where(function ($q) use ($search) {
                    $q->where('n_bloque', 'like', "%{$search}%")
                      ->orWhere('asunto', 'like', "%{$search}%");
                })
                ->with(['box.andamio.section'])
                ->whereNotNull('box_id')
                ->limit(5)
                ->get()
                ->map(function ($block) {
                    return [
                        'id' => $block->id,
                        'n_bloque' => $block->n_bloque,
                        'asunto' => $block->asunto,
                        'folios' => $block->folios,
                        'periodo' => $block->periodo,
                        'path' => [
                            'section' => $block->box?->andamio?->section?->only(['id', 'n_section', 'descripcion']),
                            'andamio' => $block->box?->andamio?->only(['id', 'n_andamio', 'descripcion']),
                            'box' => $block->box?->only(['id', 'n_box', 'descripcion']),
                        ]
                    ];
                });
        }

        return Inertia::render('storage/index', [
            'sections' => $sections->items(),
            'searchedBlocks' => $searchedBlocks,
            'pagination' => [
                'total' => $sections->total(),
                'current_page' => $sections->currentPage(),
                'last_page' => $sections->lastPage(),
                'from' => $sections->firstItem(),
                'to' => $sections->lastItem(),
            ],
            'level' => 'sections',
            'filters' => $request->only(['search']),
        ]);
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
            'n_section' => 'required|string|unique:sections,n_section,' . $section->id,
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
