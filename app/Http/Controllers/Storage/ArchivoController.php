<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storage\IndexArchivoRequest;
use App\Services\Storage\ArchivoService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ArchivoController extends Controller
{
    public function __construct(protected ArchivoService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexArchivoRequest $request, $section, $andamio, $box): Response
    {
        $resources = $this->service->getBoxWithBlocks((int) $box, $request->input('search'));

        return Inertia::render('storage/index', [
            'activeSection' => ['id' => (int) $section],
            'activeAndamio' => ['id' => (int) $andamio],
            'activeBox' => $resources['box'],
            'archivos' => $resources['blocks']->items(),
            'pagination' => [
                'total' => $resources['blocks']->total(),
                'current_page' => $resources['blocks']->currentPage(),
                'last_page' => $resources['blocks']->lastPage(),
                'from' => $resources['blocks']->firstItem(),
                'to' => $resources['blocks']->lastItem(),
            ],
            'level' => 'archivos',
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Move an archive to the default container.
     */
    public function move($section, $andamio, $box, $block): RedirectResponse
    {
        try {
            $this->service->moveToDefault((int) $box, (int) $block);
            return redirect()->back()->with('message', 'Archivo movido al contenedor default.');
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
