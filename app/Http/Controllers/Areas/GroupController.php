<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\DeleteGroupRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Services\Areas\GroupService;
use Illuminate\Http\RedirectResponse;

class GroupController extends Controller
{
    public function __construct(protected GroupService $service) {}

    /**
     * Store a newly created group in storage.
     */
    public function store(CreateGroupRequest $request): RedirectResponse
    {
        $this->service->create($request);

        return redirect()->back()->with('message', 'Grupo creado correctamente.');
    }

    /**
     * Update the specified group in storage.
     */
    public function update(UpdateGroupRequest $request, string $id): RedirectResponse
    {
        $group = $this->service->find((int) $id);
        $this->service->update($group, $request->all());

        return redirect()->back()->with('message', 'Grupo actualizado correctamente.');
    }

    /**
     * Remove the specified group from storage.
     */
    public function destroy(DeleteGroupRequest $request, string $id): RedirectResponse
    {
        $group = $this->service->find((int) $id);
        $this->service->delete($group);

        return redirect()->back()->with('message', 'Grupo eliminado correctamente.');
    }
}
