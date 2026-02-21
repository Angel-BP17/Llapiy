<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Area\CreateAreaRequest;
use App\Http\Requests\Area\IndexAreaRequest;
use App\Models\Area;
use App\Services\Areas\AreaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function __construct(protected AreaService $service)
    {
    }

    public function index(IndexAreaRequest $request)
    {
        return $this->apiSuccess('Areas obtenidas correctamente.', $this->service->getIndexData($request));
    }

    public function create()
    {
        return $this->apiError('Metodo no soportado en API.', 405);
    }

    public function store(CreateAreaRequest $request)
    {
        $this->service->create($request);

        return $this->apiSuccess('Area, grupos y subgrupos creados correctamente.', null, 201);
    }

    public function show(Area $area)
    {
        return $this->apiSuccess('Detalle de area obtenido correctamente.', $this->service->getShowData($area));
    }

    public function edit(Area $area)
    {
        return $this->apiSuccess('Area obtenida correctamente.', $this->service->getEditData($area));
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'descripcion' => [
                'required',
                'string',
                'max:255',
                Rule::unique('areas')->ignore($area->id),
            ],
            'abreviacion' => 'nullable|string|max:255',
            'grupos' => 'sometimes|array',
            'grupos.*.id' => 'nullable|integer|exists:groups,id,area_id,' . $area->id,
            'grupos.*.descripcion' => 'required_with:grupos|string|max:255',
            'grupos.*.abreviacion' => 'nullable|string|max:255',
            'grupos.*.subgrupos' => 'sometimes|array',
            'grupos.*.subgrupos.*.id' => 'nullable|integer|exists:subgroups,id,group_id,' . $request->input('grupos.*.id'),
            'grupos.*.subgrupos.*.descripcion' => 'required_with:grupos.*.subgrupos|string|max:255',
        ]);

        $this->service->update($area, $validated);

        return $this->apiSuccess('Area, grupos y subgrupos actualizados correctamente.');
    }

    public function destroy(Area $area)
    {
        $this->service->delete($area);

        return $this->apiSuccess('Area, grupos y subgrupos eliminados correctamente.');
    }
}
