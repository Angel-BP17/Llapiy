<?php

namespace App\Http\Controllers\DocumentTypes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campo\IndexCampoRequest;
use App\Http\Requests\Campo\StoreCampoRequest;
use App\Http\Requests\Campo\UpdateCampoRequest;
use App\Models\CampoType;
use App\Models\DocumentType;
use App\Services\DocumentTypes\CampoService;

class CampoController extends Controller
{
    public function __construct(protected CampoService $service)
    {
    }

    public function index(IndexCampoRequest $request)
    {
        $data = $this->service->getIndexData($request);
        $data['totalCampos'] = CampoType::count();
        $data['totalDocumentTypes'] = DocumentType::count();
        $data['dataTypes'] = CampoType::dataTypes();

        return $this->apiSuccess('Campos obtenidos correctamente.', $data);
    }

    public function store(StoreCampoRequest $request)
    {
        $this->service->create($request->validated());

        return $this->apiSuccess('Tipo de campo creado correctamente.', [
            'name' => $request->input('name'),
        ], 201);
    }

    public function update(UpdateCampoRequest $request, CampoType $campo)
    {
        $this->service->update($campo, $request->validated());

        return $this->apiSuccess('Tipo de campo actualizado correctamente.', [
            'campo' => $campo->fresh(),
        ]);
    }

    public function destroy(CampoType $campo)
    {
        $this->service->delete($campo);

        return $this->apiSuccess('Tipo de campo eliminado correctamente.');
    }
}
