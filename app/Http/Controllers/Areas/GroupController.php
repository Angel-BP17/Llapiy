<?php

namespace App\Http\Controllers\Areas;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\DocumentType;
use App\Models\Group;
use App\Models\GroupDocumentType;
use App\Models\GroupType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('admin');
            return $middleware->handle($request, $next);
        })->except('getByGroupType');
    }

    public function store(CreateGroupRequest $request)
    {
        try {
            // Buscar o crear relación area_group_type
            $areaGroupType = AreaGroupType::firstOrCreate(
                [
                    'area_id' => $request->area_id,
                    'group_type_id' => $request->group_type_id,
                ]
            );

            $groupType = GroupType::findOrFail($request->group_type_id);
            $area = Area::findOrFail($request->area_id);

            $numGroup = Group::where('area_group_type_id', $areaGroupType->id)->count() + 1;

            $descripcion = $request->descripcion ?? "{$groupType->descripcion} {$numGroup} de {$area->abreviacion}";
            $abreviacion = strtoupper(substr($groupType->descripcion, 0, 3)) . $numGroup . '_' . $area->abreviacion;


            // Crear el grupo
            $group = Group::create([
                'area_group_type_id' => $areaGroupType->id,
                'descripcion' => $descripcion,
                'abreviacion' => $abreviacion,
            ]);

            // Asignar tipo de documento "Bloque"
            $bloque = DocumentType::where('name', 'Bloque')->first();
            if ($bloque) {
                GroupDocumentType::create([
                    'document_type_id' => $bloque->id,
                    'group_id' => $group->id,
                ]);
            }
            return redirect()->back()->with('success', 'Grupo creado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('Ocurrió un error al crear el grupo.')->withInput();
        }
    }

    /**
     * Editar un grupo existente.
     */
    public function edit($id)
    {
        $group = Group::findOrFail($id);
        return view('groups.edit', compact('group'));
    }

    /**
     * Actualizar un grupo existente.
     */
    public function update(UpdateGroupRequest $request, $id)
    {
        $group = Group::findOrFail($id);
        $group->update($request->all());

        return redirect()->route('areas.show', $group->areaGroupType->area_id)->with('success', 'Grupo actualizado exitosamente.');
    }

    /**
     * Eliminar un grupo y sus subgrupos.
     */
    public function destroy($id)
    {
        $group = Group::findOrFail($id);

        // Elimina los subgrupos asociados
        $group->subgroups()->delete();

        // Elimina el grupo
        $group->delete();

        return redirect()->back()->with('success', 'Grupo eliminado exitosamente.');
    }
}
