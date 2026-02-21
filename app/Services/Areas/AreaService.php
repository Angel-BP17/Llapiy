<?php

namespace App\Services\Areas;

use App\Models\Area;
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
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('descripcion', 'LIKE', "%{$search}%")
                        ->orWhere('abreviacion', 'LIKE', "%{$search}%");
                });
            })
            ->with([
                'groups.areaGroupType.groupType',
                'groups.subgroups' => function ($query) {
                    $query->orderBy('descripcion');
                },
            ])
            ->withCount('groups');

        $areas = $query->get();

        return compact('areas');
    }

    public function create(Request $request): void
    {
        DB::transaction(function () use ($request) {
            $area = Area::create([
                'descripcion' => $request->descripcion,
                'abreviacion' => $request->abreviacion,
            ]);

            if (isset($request->grupos)) {
                foreach ($request->grupos as $grupoData) {
                    $grupo = $area->groups()->create([
                        'descripcion' => $grupoData['descripcion'],
                        'abreviacion' => $grupoData['abreviacion'] ?? null,
                    ]);

                    if (isset($grupoData['subgrupos'])) {
                        $grupo->subgroups()->createMany(
                            array_map(fn($sub) => ['descripcion' => $sub['descripcion']], $grupoData['subgrupos'])
                        );
                    }
                }
            }
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

            $existingGroupIds = [];
            if (array_key_exists('grupos', $validated)) {
                foreach ($validated['grupos'] as $grupoData) {
                    $grupo = Group::updateOrCreate(
                        ['id' => $grupoData['id'] ?? null, 'area_id' => $area->id],
                        [
                            'descripcion' => $grupoData['descripcion'],
                            'abreviacion' => $grupoData['abreviacion'] ?? null,
                        ]
                    );
                    $existingGroupIds[] = $grupo->id;

                    $existingSubgroupIds = [];
                    if (isset($grupoData['subgrupos'])) {
                        foreach ($grupoData['subgrupos'] as $subgrupoData) {
                            $subgrupo = Subgroup::updateOrCreate(
                                ['id' => $subgrupoData['id'] ?? null, 'group_id' => $grupo->id],
                                ['descripcion' => $subgrupoData['descripcion']]
                            );
                            $existingSubgroupIds[] = $subgrupo->id;
                        }
                    }

                    $grupo->subgroups()->whereNotIn('id', $existingSubgroupIds)->delete();
                }
                $area->groups()->whereNotIn('id', $existingGroupIds)->delete();
            }
        });
    }

    public function delete(Area $area): void
    {
        DB::transaction(function () use ($area) {
            $area->groups->each(function ($group) {
                $group->subgroups()->delete();
                $group->delete();
            });

            $area->delete();
        });
    }
}
