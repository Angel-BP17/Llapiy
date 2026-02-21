<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\IndexRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\Users\RoleService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(protected RoleService $service)
    {
    }

    public function index(IndexRoleRequest $request)
    {
        $data = $this->service->getIndexData($request);
        $data['permissionGroups'] = $this->service->getPermissionGroups();
        $data['permissionLabels'] = $this->service->getPermissionLabels();

        return $this->apiSuccess('Roles obtenidos correctamente.', $data);
    }

    public function store(CreateRoleRequest $request)
    {
        $role = $this->service->create(
            $request->string('name')->toString(),
            $request->input('permissions', [])
        );

        return $this->apiSuccess('Rol creado correctamente.', ['role' => $role->load('permissions')], 201);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->service->update(
            $role,
            $request->string('name')->toString(),
            $request->input('permissions', [])
        );

        return $this->apiSuccess('Rol actualizado correctamente.', ['role' => $role->fresh('permissions')]);
    }

    public function editPermissions(Role $role)
    {
        return $this->apiSuccess('Permisos del rol obtenidos correctamente.', [
            'role' => $role,
            'permissionGroups' => $this->service->getPermissionGroups(),
            'permissionLabels' => $this->service->getPermissionLabels(),
            'selectedPermissions' => $this->service->getSelectedPermissions($role),
        ]);
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $this->service->updatePermissions($role, $validated['permissions'] ?? []);

        return $this->apiSuccess('Permisos del rol actualizados correctamente.', [
            'role' => $role->fresh('permissions'),
        ]);
    }

    public function destroy(Role $role)
    {
        $this->service->delete($role);

        return $this->apiSuccess('Rol eliminado correctamente.');
    }
}
