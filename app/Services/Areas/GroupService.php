<?php

namespace App\Services\Areas;

use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\Group;
use App\Models\GroupType;
use Illuminate\Http\Request;
use DB;

class GroupService
{
    /**
     * Crea un nuevo grupo vinculado a una jerarquía de área.
     */
    public function create(Request $request): Group
    {
        return DB::transaction(function () use ($request) {
            $areaGroupType = AreaGroupType::firstOrCreate([
                'area_id' => $request->area_id,
                'group_type_id' => $request->group_type_id,
            ]);

            $groupType = GroupType::findOrFail($request->group_type_id);
            $area = Area::findOrFail($request->area_id);

            // Generación automática de descripción y abreviación si no se proporcionan
            $numGroup = Group::where('area_group_type_id', $areaGroupType->id)->count() + 1;
            $descripcion = $request->descripcion ?? "{$groupType->descripcion} {$numGroup} de {$area->abreviacion}";
            $abreviacion = $request->abreviacion ?? strtoupper(substr($groupType->descripcion, 0, 3)) . $numGroup . '_' . $area->abreviacion;

            return Group::create([
                'area_group_type_id' => $areaGroupType->id,
                'descripcion' => $descripcion,
                'abreviacion' => $abreviacion,
            ]);
        });
    }

    public function find(int $id): Group
    {
        return Group::findOrFail($id);
    }

    public function update(Group $group, array $data): Group
    {
        $group->update($data);
        return $group->fresh();
    }

    public function delete(Group $group): void
    {
        $group->subgroups()->delete();
        $group->delete();
    }
}
