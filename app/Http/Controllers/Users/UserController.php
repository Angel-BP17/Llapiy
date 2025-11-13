<?php

namespace App\Http\Controllers\Users;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\{Area, User, UserType};
use App\Services\User\UserService;
use Barryvdh\DomPDF\Facade\Pdf;
use Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage};

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('admin');
            return $middleware->handle($request, $next);
        });
    }

    public function index(Request $request)
    {
        $resources = $this->service->getAll($request);
        Cache::put('user_types_list', $resources['userTypes'], now()->addDay());
        return view('users.index', ['users' => $resources['users'], 'userTypes' => $resources['userTypes'], 'areas' => $resources['areas']]);
    }

    public function create()
    {
        return view('users.create', [
            'userTypes' => UserType::all(),
            'areas' => Area::with('areaGroupTypes.groupType', 'areaGroupTypes.groups.subgroups')->get(),
        ]);
    }

    public function store(CreateUserRequest $request)
    {
        $this->service->create($request);

        return to_route('users.index')->with('success', 'Usuario creado exitosamente');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user->load('group.areaGroupType.area', 'group.areaGroupType.groupType', 'group', 'subgroup'),
            'userTypes' => UserType::all(),
            'areas' => Area::with('areaGroupTypes.groupType', 'areaGroupTypes.groups.subgroups')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->service->update($request, $user);

        return to_route('users.index')->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(User $user)
    {
        $this->service->delete($user);

        return to_route('users.index')->with('success', 'Usuario eliminado exitosamente');
    }

    public function generatePDF(Request $request)
    {
        $users = $this->service->getUsersForDpf($request);

        return Pdf::loadView('users.report', compact('users'))
            ->setPaper('a4', 'landscape')
            ->stream('reporte_usuarios.pdf');
    }
}
