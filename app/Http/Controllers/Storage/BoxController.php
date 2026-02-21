<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storage\IndexBoxRequest;
use App\Models\Andamio;
use App\Models\Box;
use App\Models\Section;
use App\Services\Storage\BoxService;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    public function __construct(protected BoxService $service)
    {
    }

    public function index(IndexBoxRequest $request, Section $section, Andamio $andamio)
    {
        $boxes = $this->service->getByAndamio($andamio, $request->input('search'));

        return $this->apiSuccess('Cajas obtenidas correctamente.', [
            'section' => $section,
            'andamio' => $andamio,
            'boxes' => $boxes,
        ]);
    }

    public function store(Request $request, Section $section, Andamio $andamio)
    {
        $validated = $request->validate([
            'n_box' => 'required|string|unique:boxes,n_box,NULL,id,andamio_id,' . $andamio->id,
        ]);

        $this->service->create($andamio, $validated);

        return $this->apiSuccess('Caja creada correctamente.', null, 201);
    }

    public function update(Request $request, Section $section, Andamio $andamio, Box $box)
    {
        $validated = $request->validate([
            'n_box' => 'required|string|unique:boxes,n_box,' . $box->id . ',id,andamio_id,' . $andamio->id,
        ]);

        $this->service->update($box, $validated);

        return $this->apiSuccess('Caja actualizada correctamente.', ['box' => $box->fresh()]);
    }

    public function destroy(Section $section, Andamio $andamio, Box $box)
    {
        if ($box->blocks()->exists()) {
            return $this->apiError('No se puede eliminar una caja con paquetes asociados.', 422);
        }

        $this->service->delete($box);

        return $this->apiSuccess('Caja eliminada correctamente.');
    }
}
