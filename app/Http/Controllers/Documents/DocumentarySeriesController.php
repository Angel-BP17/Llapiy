<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentarySeries\IndexDocumentarySeriesRequest;
use App\Http\Requests\DocumentarySeries\CreateDocumentarySeriesRequest;
use App\Http\Requests\DocumentarySeries\UpdateDocumentarySeriesRequest;
use App\Models\DocumentarySeries;
use App\Services\DocumentarySeries\DocumentarySeriesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DocumentarySeriesController extends Controller
{
    public function __construct(protected DocumentarySeriesService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexDocumentarySeriesRequest $request): Response
    {
        // Solo administradores pueden ingresar a este CRUD
        if (!auth()->user()->hasRole('ADMINISTRADOR')) {
            abort(403, 'No tienes autorización para acceder a esta sección.');
        }

        $data = $this->service->getIndexData($request);

        return Inertia::render('documentary_series/index', [
            'documentarySeries' => $data['documentarySeries']->items(),
            'pagination' => [
                'total' => $data['documentarySeries']->total(),
                'current_page' => $data['documentarySeries']->currentPage(),
                'last_page' => $data['documentarySeries']->lastPage(),
                'from' => $data['documentarySeries']->firstItem(),
                'to' => $data['documentarySeries']->lastItem(),
            ],
            'filters' => $request->only(['codigo', 'nombre']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDocumentarySeriesRequest $request): RedirectResponse
    {
        if (!auth()->user()->hasRole('ADMINISTRADOR')) {
            abort(403, 'No tienes autorización para acceder a esta sección.');
        }

        try {
            $this->service->create($request);
            return redirect()->back()->with('message', 'Serie documental creada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear serie documental: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error al crear la serie documental.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentarySeriesRequest $request, DocumentarySeries $documentarySeries): RedirectResponse
    {
        if (!auth()->user()->hasRole('ADMINISTRADOR')) {
            abort(403, 'No tienes autorización para acceder a esta sección.');
        }

        try {
            $this->service->update($request, $documentarySeries);
            return redirect()->back()->with('message', 'Serie documental actualizada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar serie documental: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar la serie documental.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentarySeries $documentarySeries): RedirectResponse
    {
        if (!auth()->user()->hasRole('ADMINISTRADOR')) {
            abort(403, 'No tienes autorización para acceder a esta sección.');
        }

        try {
            // Validar si tiene bloques asociados antes de eliminar
            if ($documentarySeries->blocks()->exists()) {
                return redirect()->back()->with('error', 'No se puede eliminar la serie documental porque tiene bloques físicos asociados.');
            }

            $this->service->delete($documentarySeries);
            return redirect()->back()->with('message', 'Serie documental eliminada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar serie documental: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al eliminar la serie documental.');
        }
    }
}
