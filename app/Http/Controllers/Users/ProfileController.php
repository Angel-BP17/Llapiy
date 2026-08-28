<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): Response
    {
        $user = Auth::user()->load(['roles.permissions', 'permissions', 'group.areaGroupType.area', 'subgroup']);

        $permissionsMap = $user->getAllPermissions()->pluck('name')->mapWithKeys(function ($permission) {
            return [$permission => true];
        });

        return Inertia::render('profile/show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'user_name' => $user->user_name,
                'dni' => $user->dni,
                'email' => $user->email,
                'foto_perfil' => $user->foto_perfil,
                'area' => $user->group?->areaGroupType?->area?->descripcion ?? 'Sin Area',
                'grupo' => $user->group?->descripcion ?? 'Sin Grupo',
                'subgrupo' => $user->subgroup?->descripcion ?? 'Sin Subgrupo',
            ],
            'permissions' => $permissionsMap,
            'role_names' => $user->getRoleNames(),
        ]);
    }
}
