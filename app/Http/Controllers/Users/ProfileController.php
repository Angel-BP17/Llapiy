<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user()->load(['roles.permissions', 'permissions', 'group.areaGroupType.area', 'subgroup']);

        // Aplanamos todos los permisos (del usuario y de sus roles) en un solo mapa de booleanos
        $permissionsMap = $user->getAllPermissions()->pluck('name')->mapWithKeys(function ($permission) {
            return [$permission => true];
        });

        return $this->apiSuccess('Perfil obtenido correctamente.', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'user_name' => $user->user_name,
                'dni' => $user->dni,
                'email' => $user->email,
                'foto_perfil' => $user->foto_perfil,
                'area' => $user->group?->areaGroupType?->area?->descripcion ?? 'Sin Área',
                'grupo' => $user->group?->descripcion ?? 'Sin Grupo',
                'subgrupo' => $user->subgroup?->descripcion ?? 'Sin Subgrupo',
            ],
            // El Frontend solo usa esto para ocultar/mostrar botones
            'permissions' => $permissionsMap,
            // Enviamos esto solo por si el Front necesita mostrar el nombre del rol en el menú
            'role_names' => $user->getRoleNames(), 
        ]);
    }
}
