<?php

namespace App\Http\Controllers\DocumentTypes;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentType\CreateDocumentTypeRequest;
use App\Http\Requests\DocumentType\IndexDocumentTypeRequest;
use App\Http\Requests\DocumentType\UpdateDocumentTypeRequest;
use App\Models\CampoType;
use App\Models\DocumentType;
use App\Services\DocumentTypes\DocumentTypeService;
use Illuminate\Support\Facades\Log;

class DocumentTypeController extends Controller
{
    public function __construct(protected DocumentTypeService $service)
    {
    }

    public function index(IndexDocumentTypeRequest $request)
    {
        $data = $this->service->getIndexData($request);
        $data['campoTypes'] = CampoType::query()->get(['id', 'name']);
        $data['totalDocumentTypes'] = DocumentType::count();
        $data['totalCampos'] = CampoType::count();

        return $this->apiSuccess('Tipos de documento obtenidos correctamente.', $data);
    }

    public function store(CreateDocumentTypeRequest $request)
    {
        try {
            $this->service->create($request);

            return $this->apiSuccess('Tipo de documento creado correctamente.', [
                'name' => $request->input('name'),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error al crear tipo de documento: ' . $e->getMessage());

            return $this->apiError('Hubo un error al crear el tipo de documento.', 500);
        }
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType)
    {
        try {
            $this->service->update($request, $documentType);

            return $this->apiSuccess('Tipo de documento actualizado correctamente.', [
                'documentType' => $documentType->fresh(['campoTypes', 'groups', 'subgroups']),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar tipo de documento: ' . $e->getMessage());

            return $this->apiError('Ocurrio un error al actualizar el tipo de documento.', 500);
        }
    }

    public function destroy(DocumentType $documentType)
    {
        $this->service->delete($documentType);

        return $this->apiSuccess('Tipo de documento eliminado correctamente.');
    }
}
