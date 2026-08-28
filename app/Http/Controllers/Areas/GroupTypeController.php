<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupType\CreateGroupTypeRequest;
use App\Http\Requests\GroupType\UpdateGroupTypeRequest;
use App\Services\Areas\GroupTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GroupTypeController extends Controller
{
    public function __construct(protected GroupTypeService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $groupTypes = $this->service->getAll($request->search);

        return Inertia::render('group_types/index', [
            'groupTypes' => $groupTypes,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateGroupTypeRequest $request): RedirectResponse
    {
        $this->service->create($request->all());

        return redirect()->back()->with('message', 'Tipo de grupo creado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupTypeRequest $request, string $id): RedirectResponse
    {
        $groupType = $this->service->find((int) $id);
        $this->service->update($groupType, $request->all());

        return redirect()->back()->with('message', 'Tipo de grupo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $groupType = $this->service->find((int) $id);
        $this->service->delete($groupType);

        return redirect()->back()->with('message', 'Tipo de grupo eliminado correctamente.');
    }
}
