<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storage\IndexBoxRequest;
use App\Models\Andamio;
use App\Models\Box;
use App\Models\Section;
use App\Services\Storage\BoxService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BoxController extends Controller
{
    public function __construct(protected BoxService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexBoxRequest $request, Section $section, Andamio $andamio): Response
    {
        $search = $request->input('search');
        $boxes = $this->service->getByAndamio($andamio, $search);

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
            'activeAndamio' => $andamio,
            'boxes' => $boxes->items(),
            'searchedBlocks' => $searchedBlocks,
            'pagination' => [
                'total' => $boxes->total(),
                'current_page' => $boxes->currentPage(),
                'last_page' => $boxes->lastPage(),
                'from' => $boxes->firstItem(),
                'to' => $boxes->lastItem(),
            ],
            'level' => 'boxes',
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Section $section, Andamio $andamio): RedirectResponse
    {
        $validated = $request->validate([
            'n_box' => 'required|string|unique:boxes,n_box,NULL,id,andamio_id,' . $andamio->id,
        ]);

        $this->service->create($andamio, $validated);

        return redirect()->back()->with('message', 'Caja creada correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Section $section, Andamio $andamio, Box $box): RedirectResponse
    {
        $validated = $request->validate([
            'n_box' => 'required|string|unique:boxes,n_box,' . $box->id . ',id,andamio_id,' . $andamio->id,      
        ]);

        $this->service->update($box, $validated);

        return redirect()->back()->with('message', 'Caja actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section, Andamio $andamio, Box $box): RedirectResponse
    {
        if ($box->blocks()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar una caja con paquetes asociados.');   
        }

        $this->service->delete($box);

        return redirect()->back()->with('message', 'Caja eliminada correctamente.');
    }
}
