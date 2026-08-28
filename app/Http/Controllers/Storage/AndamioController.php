<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storage\IndexAndamioRequest;
use App\Models\Andamio;
use App\Models\Section;
use App\Services\Storage\AndamioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AndamioController extends Controller
{
    public function __construct(protected AndamioService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexAndamioRequest $request, Section $section): Response
    {
        $search = $request->input('search');
        $andamios = $this->service->getBySection($section, $search);

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
            'activeSection' => $section,
            'andamios' => $andamios->items(),
            'searchedBlocks' => $searchedBlocks,
            'pagination' => [
                'total' => $andamios->total(),
                'current_page' => $andamios->currentPage(),
                'last_page' => $andamios->lastPage(),
                'from' => $andamios->firstItem(),
                'to' => $andamios->lastItem(),
            ],
            'level' => 'andamios',
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Section $section): RedirectResponse
    {
        $validated = $request->validate([
            'n_andamio' => 'required|integer|unique:andamios,n_andamio,NULL,id,section_id,' . $section->id,      
            'descripcion' => 'required|string|max:255',
        ]);

        $this->service->create($section, $validated);

        return redirect()->back()->with('message', 'Andamio creado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Section $section, Andamio $andamio): RedirectResponse
    {
        $validated = $request->validate([
            'n_andamio' => 'required|integer|unique:andamios,n_andamio,' . $andamio->id . ',id,section_id,' . $section->id,
            'descripcion' => 'required|string|max:255',
        ]);

        $this->service->update($andamio, $validated);

        return redirect()->back()->with('message', 'Andamio actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section, Andamio $andamio): RedirectResponse
    {
        if ($andamio->boxes()->exists()) {
            return redirect()->back()->with('error', 'El andamio no puede ser eliminado porque contiene cajas.');
        }

        $this->service->delete($andamio);

        return redirect()->back()->with('message', 'Andamio eliminado correctamente.');
    }
}
