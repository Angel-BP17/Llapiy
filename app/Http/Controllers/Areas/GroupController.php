<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Services\Areas\GroupService;

class GroupController extends Controller
{
    public function __construct(protected GroupService $service)
    {
    }

    public function store(CreateGroupRequest $request)
    {
        try {
            $this->service->create($request);

            return $this->apiSuccess('Grupo creado correctamente.', null, 201);
        } catch (\Throwable $e) {
            return $this->apiError('Ocurrio un error al crear el grupo.', 500);
        }
    }

    public function edit($id)
    {
        $group = $this->service->find((int) $id);

        return $this->apiSuccess('Grupo obtenido correctamente.', ['group' => $group]);
    }

    public function update(UpdateGroupRequest $request, $id)
    {
        $group = $this->service->find((int) $id);
        $this->service->update($group, $request->all());

        return $this->apiSuccess('Grupo actualizado correctamente.', ['group' => $group->fresh()]);
    }

    public function destroy($id)
    {
        $group = $this->service->find((int) $id);
        $this->service->delete($group);

        return $this->apiSuccess('Grupo eliminado correctamente.');
    }
}
