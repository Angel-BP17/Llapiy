<?php

namespace App\Services\Areas;

use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\DocumentType;
use App\Models\Group;
use App\Models\GroupDocumentType;
use App\Models\GroupType;
use Illuminate\Http\Request;

class GroupService
{
    public function create(Request $request): void
    {
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

        $group = Group::create([
            'area_group_type_id' => $areaGroupType->id,
            'descripcion' => $descripcion,
            'abreviacion' => $abreviacion,
        ]);

        $bloque = DocumentType::where('name', 'Bloque')->first();
        if ($bloque) {
            GroupDocumentType::create([
                'document_type_id' => $bloque->id,
                'group_id' => $group->id,
            ]);
        }
    }

    public function find(int $id): Group
    {
        return Group::findOrFail($id);
    }

    public function update(Group $group, array $data): void
    {
        $group->update($data);
    }

    public function delete(Group $group): void
    {
        $group->subgroups()->delete();
        $group->delete();
    }
}

