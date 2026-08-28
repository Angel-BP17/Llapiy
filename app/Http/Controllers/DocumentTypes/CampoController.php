<?php

namespace App\Http\Controllers\DocumentTypes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campo\IndexCampoRequest;
use App\Http\Requests\Campo\StoreCampoRequest;
use App\Http\Requests\Campo\UpdateCampoRequest;
use App\Models\CampoType;
use App\Models\DocumentType;
use App\Services\DocumentTypes\CampoService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CampoController extends Controller
{
    public function __construct(protected CampoService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexCampoRequest $request): Response
    {
        $data = $this->service->getIndexData($request);

        return Inertia::render('campos/index', [
            'campos' => $data['campos'],
            'totalCampos' => CampoType::count(),
            'totalDocumentTypes' => DocumentType::count(),
            'dataTypes' => CampoType::dataTypes(),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampoRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->back()->with('message', 'Tipo de campo creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CampoType $campo): Response
    {
        return Inertia::render('campos/show', [
            'campo' => $campo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampoRequest $request, CampoType $campo): RedirectResponse
    {
        $this->service->update($campo, $request->validated());

        return redirect()->back()->with('message', 'Tipo de campo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampoType $campo): RedirectResponse
    {
        try {
            $this->service->delete($campo);
            return redirect()->back()->with('message', 'Tipo de campo eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
