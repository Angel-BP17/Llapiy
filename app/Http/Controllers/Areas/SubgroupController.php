<?php

namespace App\Http\Controllers\Areas;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Http\Requests\Subgroup\CreateSubgroupRequest;
use App\Http\Requests\Subgroup\UpdateSubgroupRequest;
use App\Models\DocumentType;
use App\Models\Subgroup;
use App\Models\SubgroupDocumentType;
use App\Http\Controllers\Controller;

class SubgroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('admin');
            return $middleware->handle($request, $next);
        })->except('getByGroup');
    }
    public function store(CreateSubgroupRequest $request)
    {
        // Crear el subgrupo
        $subgroup = Subgroup::create([
            'group_id' => $request->group_id,
            'descripcion' => $request->descripcion ?? 'Nuevo Subgrupo',
            'abreviacion' => $request->abreviacion,
            'parent_subgroup_id' => $request->parent_subgroup_id,
        ]);

        $bloque = DocumentType::where('name', 'Bloque')->first();

        SubgroupDocumentType::create([
            'subgroup_id' => $subgroup->id,
            'document_type_id' => $bloque->id,
        ]);

        return redirect()->back()->with('success', 'Subgrupo creado exitosamente.');
    }

    /**
     * Editar un subgrupo existente.
     */
    public function edit($id)
    {
        $subgroup = Subgroup::findOrFail($id);
        return view('subgroups.edit', compact('subgroup'));
    }

    /**
     * Actualizar un subgrupo existente.
     */
    public function update(UpdateSubgroupRequest $request, $id)
    {
        $subgroup = Subgroup::findOrFail($id);
        $subgroup->update($request->all());

        return redirect()->route('areas.show', $subgroup->group->areaGroupType->area->id)->with('success', 'Subgrupo actualizado exitosamente.');
    }

    /**
     * Eliminar un subgrupo.
     */
    public function destroy($id)
    {
        $subgroup = Subgroup::findOrFail($id);
        $subgroup->delete();

        return redirect()->back()->with('success', 'Subgrupo eliminado exitosamente.');
    }
}
