<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Area;
use App\Models\User;
use App\Services\User\UserService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
    }

    public function index(IndexUserRequest $request)
    {
        $resources = $this->service->getAll($request);

        return $this->apiSuccess('Usuarios obtenidos correctamente.', [
            'users' => $resources['users'],
            'areas' => $resources['areas'],
            'roles' => Role::query()->orderBy('name')->get(),
            'totalUsers' => User::count(),
            'totalRoles' => Role::count(),
            'totalAreas' => Area::count(),
        ]);
    }

    public function store(CreateUserRequest $request)
    {
        $user = $this->service->create($request->validated(), $request->file('foto_perfil'));

        return $this->apiSuccess('Usuario creado correctamente.', ['user' => $user], 201);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->service->update($request, $user);

        return $this->apiSuccess('Usuario actualizado correctamente.', ['user' => $user->fresh(['roles'])]);
    }

    public function destroy(User $user)
    {
        $this->service->delete($user);

        return $this->apiSuccess('Usuario eliminado correctamente.');
    }

    public function generatePDF(Request $request)
    {
        $users = $this->service->getUsersForDpf($request);

        return Pdf::loadView('users.report', compact('users'))
            ->setPaper('a4', 'landscape')
            ->stream('reporte_usuarios.pdf');
    }
}
