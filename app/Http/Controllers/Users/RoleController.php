<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\IndexRoleRequest;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\Users\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct(protected RoleService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRoleRequest $request): Response
    {
        $data = $this->service->getIndexData($request);
        $allPermissions = Permission::pluck('name')->toArray();

        $roles = $data['roles']->items();
        foreach ($roles as $role) {
            $role->permission_list = $role->permissions->pluck('name')->toArray();
        }

        return Inertia::render('roles/index', [
            'roles' => $roles,
            'permissions' => $allPermissions,
            'pagination' => [
                'total' => $data['roles']->total(),
                'current_page' => $data['roles']->currentPage(),
                'last_page' => $data['roles']->lastPage(),
                'from' => $data['roles']->firstItem(),
                'to' => $data['roles']->lastItem(),
            ],
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRoleRequest $request): RedirectResponse
    {
        $this->service->create(
            $request->string('name')->toString(),
            $request->input('permissions', [])
        );

        return redirect()->back()->with('message', 'Rol creado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->service->update(
            $role,
            $request->string('name')->toString(),
            $request->input('permissions', [])
        );

        return redirect()->back()->with('message', 'Rol actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->service->delete($role);

        return redirect()->back()->with('message', 'Rol eliminado correctamente.');
    }

    /**
     * Update permissions for the specified role.
     */
    public function updatePermissions(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $this->service->updatePermissions($role, $validated['permissions'] ?? []);

        return redirect()->back()->with('message', 'Permisos del rol actualizados correctamente.');
    }
}
