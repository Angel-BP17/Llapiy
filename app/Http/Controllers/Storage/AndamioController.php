<?php

namespace App\Http\Controllers\Storage;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Models\Andamio;
use App\Models\Section;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AndamioController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('encargado');
            return $middleware->handle($request, $next);
        });
    }
    public function index(Section $section)
    {
        // Obtener los andamios asociados a la sección
        $andamios = $section->andamios;

        return view('andamios.index', compact('section', 'andamios'));
    }

    /**
     * Almacena un nuevo andamio en la sección especificada.
     */
    public function store(Request $request, Section $section)
    {

        // Validar los datos del formulario
        $request->validate([
            'n_andamio' => 'required|integer|unique:andamios,n_andamio,NULL,id,section_id,' . $section->id,
            'descripcion' => 'required|string|max:255',
        ]);

        // Crear un nuevo andamio asociado a la sección
        $section->andamios()->create($request->all());

        return redirect()->route('sections.andamios.index', ['section' => $section->id])
            ->with('success', 'Andamio creado con éxito');
    }

    /**
     * Actualiza un andamio existente.
     */
    public function update(Request $request, Section $section, Andamio $andamio)
    {

        // Validar los datos del formulario
        $request->validate([
            'n_andamio' => 'required|integer|unique:andamios,n_andamio,' . $andamio->id . ',id,section_id,' . $section->id,
            'descripcion' => 'required|string|max:255',
        ]);

        // Actualizar el andamio
        $andamio->update($request->all());

        return redirect()->route('sections.andamios.index', ['section' => $section->id])
            ->with('success', 'Andamio actualizado con éxito');
    }

    /**
     * Elimina un andamio si no contiene cajas.
     */
    public function destroy(Section $section, Andamio $andamio)
    {
        // Verificar si el andamio contiene cajas
        if ($andamio->boxes()->exists()) {
            return redirect()->route('sections.andamios.index', ['section' => $section->id])
                ->withErrors('El andamio no puede ser eliminado porque contiene cajas.');
        }

        // Eliminar el andamio
        $andamio->delete();

        return redirect()->route('sections.andamios.index', ['section' => $section->id])
            ->with('success', 'Andamio eliminado con éxito');
    }
}
