<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storage\IndexSectionRequest;
use App\Models\Section;
use App\Services\Storage\SectionService;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct(protected SectionService $service)
    {
    }

    public function index(IndexSectionRequest $request)
    {
        $sections = $this->service->getAll($request->input('search'));

        return $this->apiSuccess('Secciones obtenidas correctamente.', ['sections' => $sections]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'n_section' => 'required|string|unique:sections,n_section',
            'descripcion' => 'required|string|max:255',
        ]);

        $this->service->create($validated);

        return $this->apiSuccess('Seccion creada correctamente.', null, 201);
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'n_section' => 'required|string|unique:sections,n_section,' . $section->id,
            'descripcion' => 'required|string|max:255',
        ]);

        $this->service->update($section, $validated);

        return $this->apiSuccess('Seccion actualizada correctamente.', ['section' => $section->fresh()]);
    }

    public function destroy(Section $section)
    {
        $this->service->delete($section);

        return $this->apiSuccess('Seccion eliminada correctamente.');
    }
}
