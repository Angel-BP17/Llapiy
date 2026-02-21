<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupType\CreateGroupTypeRequest;
use App\Http\Requests\GroupType\IndexGroupTypeRequest;
use App\Http\Requests\GroupType\UpdateGroupTypeRequest;
use App\Services\Areas\GroupTypeService;

class GroupTypeController extends Controller
{
    public function __construct(protected GroupTypeService $service)
    {
    }

    public function index(IndexGroupTypeRequest $request)
    {
        $groupTypes = $this->service->getAll($request->input('search'));

        return $this->apiSuccess('Tipos de grupo obtenidos correctamente.', ['groupTypes' => $groupTypes]);
    }

    public function create()
    {
        return $this->apiError('Metodo no soportado en API.', 405);
    }

    public function store(CreateGroupTypeRequest $request)
    {
        $this->service->create($request->all());

        return $this->apiSuccess('Tipo de grupo creado correctamente.', null, 201);
    }

    public function edit($id)
    {
        $groupType = $this->service->find((int) $id);

        return $this->apiSuccess('Tipo de grupo obtenido correctamente.', ['groupType' => $groupType]);
    }

    public function update(UpdateGroupTypeRequest $request, $id)
    {
        $groupType = $this->service->find((int) $id);
        $this->service->update($groupType, $request->all());

        return $this->apiSuccess('Tipo de grupo actualizado correctamente.', ['groupType' => $groupType->fresh()]);
    }

    public function destroy($id)
    {
        $groupType = $this->service->find((int) $id);

        if (!$groupType->canBeDeleted()) {
            return $this->apiError('No se puede eliminar este tipo de grupo porque tiene grupos asociados.', 422);
        }

        $this->service->delete($groupType);

        return $this->apiSuccess('Tipo de grupo eliminado correctamente.');
    }
}
