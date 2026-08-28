<?php

namespace App\Http\Controllers\DocumentTypes;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentType\IndexDocumentTypeRequest;
use App\Http\Requests\DocumentType\CreateDocumentTypeRequest;
use App\Http\Requests\DocumentType\UpdateDocumentTypeRequest;
use App\Models\CampoType;
use App\Models\DocumentType;
use App\Services\DocumentTypes\DocumentTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DocumentTypeController extends Controller
{
    public function __construct(protected DocumentTypeService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexDocumentTypeRequest $request): Response
    {
        $data = $this->service->getIndexData($request);
        $documentTypes = $data['documentTypes'];

        return Inertia::render('document_types/index', [
            'documentTypes' => $documentTypes->items(),
            'areas' => $data['areas'],
            'campoTypes' => CampoType::query()->get(['id', 'name']),
            'paginationData' => [
                'total' => $documentTypes->total(),
                'current_page' => $documentTypes->currentPage(),
                'last_page' => $documentTypes->lastPage(),
                'from' => $documentTypes->firstItem(),
                'to' => $documentTypes->lastItem(),
            ],
            'filters' => $request->only(['name', 'area_id', 'group_id', 'subgroup_id']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDocumentTypeRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request);
            return redirect()->back()->with('message', 'Tipo de documento creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear tipo de documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error al crear el tipo de documento.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentType $documentType): Response
    {
        $documentType->load(['campoTypes', 'groups', 'subgroups']);

        return Inertia::render('document_types/show', [
            'documentType' => $documentType
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType): RedirectResponse
    {
        try {
            $this->service->update($request, $documentType);
            return redirect()->back()->with('message', 'Tipo de documento actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar tipo de documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el tipo de documento.');    
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentType $documentType): RedirectResponse
    {
        $this->service->delete($documentType);
        return redirect()->back()->with('message', 'Tipo de documento eliminado correctamente.');
    }
}
