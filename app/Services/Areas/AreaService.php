<?php

namespace App\Services\Areas;

use App\Models\Area;
use App\Models\AreaGroupType;
use App\Models\Group;
use App\Models\GroupType;
use App\Models\Subgroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaService
{
    public function getIndexData(Request $request): array
    {
        $query = Area::query()
            ->select('id', 'descripcion', 'abreviacion')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('descripcion', 'LIKE', "%{$search}%")
                        ->orWhere('abreviacion', 'LIKE', "%{$search}%");
                });
            })
            ->with([
                'groups:groups.id,groups.area_group_type_id,groups.descripcion,groups.abreviacion',
                'groups.areaGroupType:id,area_id,group_type_id',
                'groups.areaGroupType.groupType:id,descripcion',
                'groups.subgroups' => function ($query) {
                    $query->select('subgroups.id', 'subgroups.group_id', 'subgroups.descripcion', 'subgroups.abreviacion')->orderBy('subgroups.descripcion');
                },
            ])
            ->withCount('groups')
            ->orderBy('descripcion');

        $areas = $query->paginate(10)->withQueryString();

        return compact('areas');
    }

    public function create(Request $request): Area
    {
        return DB::transaction(function () use ($request) {
            $area = Area::create([
                'descripcion' => $request->descripcion,
                'abreviacion' => $request->abreviacion,
            ]);

            if (isset($request->grupos)) {
                // Obtenemos un tipo de grupo por defecto para los grupos creados desde el área
                $defaultGroupType = GroupType::firstOrCreate(
                    ['abreviacion' => 'EQU'],
                    ['descripcion' => 'Equipos']
                );

                $areaGroupType = AreaGroupType::firstOrCreate([
                    'area_id' => $area->id,
                    'group_type_id' => $defaultGroupType->id
                ]);

                foreach ($request->grupos as $grupoData) {
                    $grupo = Group::create([
                        'area_group_type_id' => $areaGroupType->id,
                        'descripcion' => $grupoData['descripcion'],
                        'abreviacion' => $grupoData['abreviacion'] ?? strtoupper(substr($grupoData['descripcion'], 0, 3)),
                    ]);

                    if (isset($grupoData['subgrupos'])) {
                        $grupo->subgroups()->createMany(
                            array_map(fn($sub) => [
                                'descripcion' => $sub['descripcion'],
                                'abreviacion' => $sub['abreviacion'] ?? strtoupper(substr($sub['descripcion'], 0, 3)),
                            ], $grupoData['subgrupos'])
                        );
                    }
                }
            }

            return $area;
        });
    }

    public function getShowData(Area $area): array
    {
        $groupTypes = GroupType::all();

        $area->load([
            'groups.areaGroupType.groupType',
            'groups.subgroups' => function ($query) {
                $query->orderBy('descripcion');
            },
        ]);

        return compact('area', 'groupTypes');
    }

    public function getEditData(Area $area): array
    {
        $area->load([
            'groups.subgroups' => function ($query) {
                $query->orderBy('descripcion');
            },
        ]);

        return compact('area');
    }

    public function update(Area $area, array $validated): void
    {
        DB::transaction(function () use ($validated, $area) {
            $area->update([
                'descripcion' => $validated['descripcion'],
                'abreviacion' => $validated['abreviacion'],
            ]);

            if (array_key_exists('grupos', $validated)) {
                $defaultGroupType = GroupType::firstOrCreate(
                    ['abreviacion' => 'EQU'],
                    ['descripcion' => 'Equipos']
                );

                $areaGroupType = AreaGroupType::firstOrCreate([
                    'area_id' => $area->id,
                    'group_type_id' => $defaultGroupType->id
                ]);

                $existingGroupIds = [];
                foreach ($validated['grupos'] as $grupoData) {
                    $grupo = Group::updateOrCreate(
                        ['id' => $grupoData['id'] ?? null],
                        [
                            'area_group_type_id' => $areaGroupType->id,
                            'descripcion' => $grupoData['descripcion'],
                            'abreviacion' => $grupoData['abreviacion'] ?? strtoupper(substr($grupoData['descripcion'], 0, 3)),
                        ]
                    );
                    $existingGroupIds[] = $grupo->id;

                    $existingSubgroupIds = [];
                    if (isset($grupoData['subgrupos'])) {
                        foreach ($grupoData['subgrupos'] as $subgrupoData) {
                            $subgrupo = Subgroup::updateOrCreate(
                                ['id' => $subgrupoData['id'] ?? null, 'group_id' => $grupo->id],
                                [
                                    'descripcion' => $subgrupoData['descripcion'],
                                    'abreviacion' => $subgrupoData['abreviacion'] ?? strtoupper(substr($subgrupoData['descripcion'], 0, 3)),
                                ]
                            );
                            $existingSubgroupIds[] = $subgrupo->id;
                        }
                    }

                    $grupo->subgroups()->whereNotIn('id', $existingSubgroupIds)->delete();
                }
                
                // Nota: Esto solo borra grupos que pertenecen al AreaGroupType por defecto.
                // Si hay grupos en otros AreaGroupTypes, no se verán afectados aquí.
                // Esto es una simplificación razonable dado el contrato actual de la API.
                $areaGroupType->groups()->whereNotIn('id', $existingGroupIds)->delete();
            }
        });
    }

    public function delete(Area $area): void
    {
        DB::transaction(function () use ($area) {
            $groupIds = $area->groups()->pluck('id');
            
            \App\Models\Subgroup::whereIn('group_id', $groupIds)->delete();
            $area->groups()->delete();
            $area->delete();
        });
    }
}
