<?php

namespace App\Http\Controllers\Areas;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Http\Requests\GroupType\CreateGroupTypeRequest;
use App\Http\Requests\GroupType\UpdateGroupTypeRequest;
use App\Models\GroupType;
use App\Http\Controllers\Controller;

class GroupTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('admin');
            return $middleware->handle($request, $next);
        })->except('getByArea');
    }
    public function index()
    {
        $groupTypes = GroupType::all();
        return view('group_types.index', compact('groupTypes'));
    }

    /**
     * Mostrar el formulario para crear un nuevo tipo de grupo.
     */
    public function create()
    {
        return view('group_types.create');
    }

    /**
     * Guardar un nuevo tipo de grupo en la base de datos.
     */
    public function store(CreateGroupTypeRequest $request)
    {
        GroupType::create($request->all());

        return redirect()->route('group_types.index')->with('success', 'Tipo de grupo creado exitosamente.');
    }

    /**
     * Mostrar el formulario para editar un tipo de grupo existente.
     */
    public function edit($id)
    {
        $groupType = GroupType::findOrFail($id);
        return view('group_types.edit', compact('groupType'));
    }

    /**
     * Actualizar un tipo de grupo existente en la base de datos.
     */
    public function update(UpdateGroupTypeRequest $request, $id)
    {
        $groupType = GroupType::findOrFail($id);
        $groupType->update($request->all());

        return redirect()->route('group_types.index')->with('success', 'Tipo de grupo actualizado exitosamente.');
    }

    /**
     * Eliminar un tipo de grupo existente.
     */
    public function destroy($id)
    {
        $groupType = GroupType::findOrFail($id);

        if (!$groupType->canBeDeleted()) {
            return redirect()->back()->with('error', 'No se puede eliminar este tipo de grupo porque tiene grupos asociados.');
        }

        $groupType->delete();

        return redirect()->route('group_types.index')->with('success', 'Tipo de grupo eliminado exitosamente.');
    }
}
