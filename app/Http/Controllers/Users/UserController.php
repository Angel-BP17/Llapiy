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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(protected UserService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexUserRequest $request): Response
    {
        $resources = $this->service->getAll($request);

        return Inertia::render('users/index', [
            'users' => $resources['users']->items(),
            'areas' => $resources['areas'],
            'roles' => Role::query()->orderBy('name')->get()->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'label' => ucfirst($role->name),
            ]),
            'stats' => [
                'totalUsers' => User::count(),
                'totalRoles' => Role::count(),
                'totalAreas' => Area::count(),
            ],
            'pagination' => [
                'total' => $resources['users']->total(),
                'current_page' => $resources['users']->currentPage(),
                'last_page' => $resources['users']->lastPage(),
                'from' => $resources['users']->firstItem(),
                'to' => $resources['users']->lastItem(),
            ],
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        $this->service->create($request->validated(), $request->file('foto_perfil'));

        return redirect()->back()->with('message', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        $user->load(['roles', 'group.areaGroupType.area']);

        return Inertia::render('users/show', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->service->update($request, $user);

        return redirect()->back()->with('message', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->service->delete($user);

        return redirect()->back()->with('message', 'Usuario eliminado correctamente.');
    }

    /**
     * View/Download user profile photo from private storage.
     */
    public function photo(User $user)
    {
        $rawPath = $user->getRawOriginal('foto_perfil') ?? $user->foto_perfil;

        if (! $rawPath) {
            abort(404, 'Foto de perfil no encontrada.');
        }

        if (Storage::disk('local')->exists($rawPath)) {
            return Storage::disk('local')->response($rawPath);
        }

        if (Storage::disk('public')->exists($rawPath)) {
            return Storage::disk('public')->response($rawPath);
        }

        abort(404, 'Archivo de foto de perfil no encontrado.');
    }

    /**
     * Generate PDF report.
     */
    public function pdf(Request $request)
    {
        try {
            $users = User::with(['roles', 'group.areaGroupType.area'])->get();

            return Pdf::loadView('users.report', compact('users'))
                ->setPaper('a4', 'landscape')
                ->stream('reporte_usuarios.pdf');
        } catch (\Throwable) {
            return response()->json(['message' => 'No se pudo generar el reporte de usuarios.'], 500);
        }
    }
}
