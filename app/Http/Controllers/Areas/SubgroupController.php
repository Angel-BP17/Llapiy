<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subgroup\CreateSubgroupRequest;
use App\Http\Requests\Subgroup\UpdateSubgroupRequest;
use App\Services\Areas\SubgroupService;

class SubgroupController extends Controller
{
    public function __construct(protected SubgroupService $service)
    {
    }

    public function store(CreateSubgroupRequest $request)
    {
        $this->service->create($request);

        return $this->apiSuccess('Subgrupo creado correctamente.', null, 201);
    }

    public function edit($id)
    {
        $subgroup = $this->service->find((int) $id);

        return $this->apiSuccess('Subgrupo obtenido correctamente.', ['subgroup' => $subgroup]);
    }

    public function update(UpdateSubgroupRequest $request, $id)
    {
        $subgroup = $this->service->find((int) $id);
        $this->service->update($subgroup, $request->all());

        return $this->apiSuccess('Subgrupo actualizado correctamente.', ['subgroup' => $subgroup->fresh()]);
    }

    public function destroy($id)
    {
        $subgroup = $this->service->find((int) $id);
        $this->service->delete($subgroup);

        return $this->apiSuccess('Subgrupo eliminado correctamente.');
    }
}
