<?php

namespace App\Http\Controllers\Storage;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Models\Section;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('encargado');
            return $middleware->handle($request, $next);
        });
    }
    public function index()
    {
        $sections = Section::all();
        return view('sections.index', compact('sections'));
    }

    /**
     * Crea una nueva sección.
     */
    public function store(Request $request)
    {

        // Validar los datos del formulario
        $request->validate([
            'n_section' => 'required|string|unique:sections,n_section',
            'descripcion' => 'required|string|max:255',
        ]);

        // Crear una nueva sección
        Section::create($request->all());
        return redirect()->route('sections.index')->with('success', 'Sección creada con éxito');
    }

    /**
     * Actualiza una sección existente.
     */
    public function update(Request $request, Section $section)
    {

        // Validar los datos
        $request->validate([
            'n_section' => 'required|string|unique:sections,n_section,' . $section->id,
            'descripcion' => 'required|string|max:255',
        ]);

        // Actualizar la sección
        $section->update($request->all());
        return redirect()->route('sections.index')->with('success', 'Sección actualizada con éxito');
    }

    /**
     * Elimina una sección.
     */
    public function destroy(Section $section)
    {

        // Eliminar la sección
        $section->delete();
        return redirect()->route('sections.index')->with('success', 'Sección eliminada con éxito');
    }
}
