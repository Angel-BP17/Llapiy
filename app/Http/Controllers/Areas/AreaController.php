<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Area\CreateAreaRequest;
use App\Http\Requests\Area\DeleteAreaRequest;
use App\Http\Requests\Area\IndexAreaRequest;
use App\Http\Requests\Area\UpdateAreaRequest;
use App\Models\Area;
use App\Models\Group;
use App\Models\GroupType;
use App\Models\Subgroup;
use App\Services\Areas\AreaService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AreaController extends Controller
{
    public function __construct(protected AreaService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexAreaRequest $request): Response
    {
        $data = $this->service->getIndexData($request);
        $areas = $data['areas'];

        return Inertia::render('areas/index', [
            'areas' => $areas->items(),
            'groups' => Group::with('areaGroupType')->withCount(['subgroups', 'users', 'documentTypes'])->get()->map(function ($group) {
                $group->area_id = $group->areaGroupType?->area_id;
                $group->group_type_id = $group->areaGroupType?->group_type_id;

                return $group;
            }),
            'subgroups' => Subgroup::withCount(['subgroups', 'users', 'documentTypes'])->get(),
            'groupTypes' => GroupType::all(),
            'pagination' => [
                'total' => $areas->total(),
                'current_page' => $areas->currentPage(),
                'last_page' => $areas->lastPage(),
                'from' => $areas->firstItem(),
                'to' => $areas->lastItem(),
            ],
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAreaRequest $request): RedirectResponse
    {
        $this->service->create($request);

        return redirect()->back()->with('message', 'Área creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area): Response
    {
        return Inertia::render('areas/show', [
            'area' => $area->load(['groups.subgroups']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAreaRequest $request, Area $area): RedirectResponse
    {
        $this->service->update($area, $request->all());

        return redirect()->back()->with('message', 'Área actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteAreaRequest $request, Area $area): RedirectResponse
    {
        $this->service->delete($area);

        return redirect()->back()->with('message', 'Área eliminada correctamente.');
    }
}
