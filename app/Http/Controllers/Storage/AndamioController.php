<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storage\IndexAndamioRequest;
use App\Models\Andamio;
use App\Models\Section;
use App\Services\Storage\AndamioService;
use Illuminate\Http\Request;

class AndamioController extends Controller
{
    public function __construct(protected AndamioService $service)
    {
    }

    public function index(IndexAndamioRequest $request, Section $section)
    {
        $andamios = $this->service->getBySection($section, $request->input('search'));

        return $this->apiSuccess('Andamios obtenidos correctamente.', [
            'section' => $section,
            'andamios' => $andamios,
        ]);
    }

    public function store(Request $request, Section $section)
    {
        $validated = $request->validate([
            'n_andamio' => 'required|integer|unique:andamios,n_andamio,NULL,id,section_id,' . $section->id,
            'descripcion' => 'required|string|max:255',
        ]);

        $this->service->create($section, $validated);

        return $this->apiSuccess('Andamio creado correctamente.', null, 201);
    }

    public function update(Request $request, Section $section, Andamio $andamio)
    {
        $validated = $request->validate([
            'n_andamio' => 'required|integer|unique:andamios,n_andamio,' . $andamio->id . ',id,section_id,' . $section->id,
            'descripcion' => 'required|string|max:255',
        ]);

        $this->service->update($andamio, $validated);

        return $this->apiSuccess('Andamio actualizado correctamente.', ['andamio' => $andamio->fresh()]);
    }

    public function destroy(Section $section, Andamio $andamio)
    {
        if ($andamio->boxes()->exists()) {
            return $this->apiError('El andamio no puede ser eliminado porque contiene cajas.', 422);
        }

        $this->service->delete($andamio);

        return $this->apiSuccess('Andamio eliminado correctamente.');
    }
}
