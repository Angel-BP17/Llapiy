<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\CreatePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Services\Users\PermissionService;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct(protected PermissionService $service)
    {
    }

    public function create()
    {
        return $this->apiError('Metodo no soportado en API.', 405);
    }

    public function store(CreatePermissionRequest $request)
    {
        $permission = $this->service->create($request->string('name')->toString());

        return $this->apiSuccess('Permiso creado exitosamente.', [
            'permission' => $permission,
        ], 201);
    }

    public function edit(Permission $permission)
    {
        return $this->apiSuccess('Permiso obtenido exitosamente.', [
            'permission' => $permission,
        ]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $this->service->update($permission, $request->string('name')->toString());

        return $this->apiSuccess('Permiso actualizado exitosamente.', [
            'permission' => $permission->fresh(),
        ]);
    }

    public function destroy(Permission $permission)
    {
        $this->service->delete($permission);

        return $this->apiSuccess('Permiso eliminado exitosamente.');
    }
}
