<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subgroup\CreateSubgroupRequest;
use App\Http\Requests\Subgroup\DeleteSubgroupRequest;
use App\Http\Requests\Subgroup\UpdateSubgroupRequest;
use App\Services\Areas\SubgroupService;
use Illuminate\Http\RedirectResponse;

class SubgroupController extends Controller
{
    public function __construct(protected SubgroupService $service) {}

    /**
     * Store a newly created subgroup in storage.
     */
    public function store(CreateSubgroupRequest $request): RedirectResponse
    {
        $this->service->create($request);

        return redirect()->back()->with('message', 'Subgrupo creado correctamente.');
    }

    /**
     * Update the specified subgroup in storage.
     */
    public function update(UpdateSubgroupRequest $request, string $id): RedirectResponse
    {
        $subgroup = $this->service->find((int) $id);
        $this->service->update($subgroup, $request->all());

        return redirect()->back()->with('message', 'Subgrupo actualizado correctamente.');
    }

    /**
     * Remove the specified subgroup from storage.
     */
    public function destroy(DeleteSubgroupRequest $request, string $id): RedirectResponse
    {
        $subgroup = $this->service->find((int) $id);
        $this->service->delete($subgroup);

        return redirect()->back()->with('message', 'Subgrupo eliminado correctamente.');
    }
}
