<?php

namespace App\Http\Controllers\Areas;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Http\Requests\Area\CreateAreaRequest;
use App\Models\{Area, Group, Subgroup};
use App\Models\GroupType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('admin');
            return $middleware->handle($request, $next);
        });
    }

    public function index(Request $request)
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
                }
            ]);

        $areas = $query->get();

        return view('areas.index', compact('areas'));
    }

    public function create()
    {
        return view('areas.create');
    }

    public function store(CreateAreaRequest $request)
    {

        return DB::transaction(function () use ($request) {
            $area = Area::create([
                'descripcion' => $request->descripcion,
                'abreviacion' => $request->abreviacion
            ]);

            if (isset($request->grupos)) {
                foreach ($request->grupos as $grupoData) {
                    $grupo = $area->groups()->create([
                        'descripcion' => $grupoData['descripcion'],
                        'abreviacion' => $grupoData['abreviacion'] ?? null
                    ]);

                    if (isset($grupoData['subgrupos'])) {
                        $grupo->subgroups()->createMany(
                            array_map(fn($sub) => ['descripcion' => $sub['descripcion']], $grupoData['subgrupos'])
                        );
                    }
                }
            }

            return redirect()->route('areas.index')
                ->with('success', 'Área, grupos y subgrupos creados exitosamente.');
        });
    }

    public function show(Area $area)
    {
        $groupTypes = GroupType::all();

        $area->load([
            'groups.areaGroupType.groupType',
            'groups.subgroups' => function ($query) {
                $query->orderBy('descripcion');
            }
        ]);

        return view('areas.show', compact('area', 'groupTypes'));
    }

    public function edit(Area $area)
    {
        $area->load([
            'groups.subgroups' => function ($query) {
                $query->orderBy('descripcion');
            }
        ]);

        return view('areas.edit', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'descripcion' => [
                'required',
                'string',
                'max:255',
                Rule::unique('areas')->ignore($area->id)
            ],
            'abreviacion' => 'nullable|string|max:255',
            'grupos' => 'sometimes|array',
            'grupos.*.id' => 'nullable|integer|exists:groups,id,area_id,' . $area->id,
            'grupos.*.descripcion' => 'required_with:grupos|string|max:255',
            'grupos.*.abreviacion' => 'nullable|string|max:255',
            'grupos.*.subgrupos' => 'sometimes|array',
            'grupos.*.subgrupos.*.id' => 'nullable|integer|exists:subgroups,id,group_id,' . $request->input('grupos.*.id'),
            'grupos.*.subgrupos.*.descripcion' => 'required_with:grupos.*.subgrupos|string|max:255'
        ]);

        return DB::transaction(function () use ($validated, $area) {
            $area->update([
                'descripcion' => $validated['descripcion'],
                'abreviacion' => $validated['abreviacion']
            ]);

            $existingGroupIds = [];
            if (isset($validated['grupos'])) {
                foreach ($validated['grupos'] as $grupoData) {
                    $grupo = Group::updateOrCreate(
                        ['id' => $grupoData['id'] ?? null, 'area_id' => $area->id],
                        [
                            'descripcion' => $grupoData['descripcion'],
                            'abreviacion' => $grupoData['abreviacion'] ?? null
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

                    // Eliminar subgrupos no incluidos
                    $grupo->subgroups()->whereNotIn('id', $existingSubgroupIds)->delete();
                }
            }

            // Eliminar grupos no incluidos
            $area->groups()->whereNotIn('id', $existingGroupIds)->delete();

            return redirect()->route('areas.index')
                ->with('success', 'Área, grupos y subgrupos actualizados exitosamente.');
        });
    }

    public function destroy(Area $area)
    {
        return DB::transaction(function () use ($area) {
            $area->groups->each(function ($group) {
                $group->subgroups()->delete();
                $group->delete();
            });

            $area->delete();

            return redirect()->route('areas.index')
                ->with('success', 'Área, grupos y subgrupos eliminados exitosamente.');
        });
    }
}