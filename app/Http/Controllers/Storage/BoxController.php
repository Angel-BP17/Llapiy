<?php

namespace App\Http\Controllers\Storage;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Models\Andamio;
use App\Models\Box;
use App\Models\Section;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('encargado');
            return $middleware->handle($request, $next);
        });
    }
    public function index(Section $section, Andamio $andamio)
    {

        $boxes = $andamio->boxes;
        return view('boxes.index', compact('section', 'andamio', 'boxes'));
    }

    public function store(Request $request, Section $section, Andamio $andamio)
    {

        $request->validate([
            'n_box' => 'required|string|unique:boxes,n_box,NULL,id,andamio_id,' . $andamio->id,
        ]);

        $andamio->boxes()->create($request->only('n_box'));
        return redirect()->route('sections.andamios.boxes.index', ['section' => $section->id, 'andamio' => $andamio->id])
            ->with('success', 'Caja registrada correctamente.');
    }

    public function update(Request $request, Section $section, Andamio $andamio, Box $box)
    {

        $request->validate([
            'n_box' => 'required|string|unique:boxes,n_box,' . $box->id . ',id,andamio_id,' . $andamio->id
        ]);

        $box->update($request->only('n_box'));
        return redirect()->route('sections.andamios.boxes.index', ['section' => $section->id, 'andamio' => $andamio->id])
            ->with('success', 'Caja actualizada correctamente.');
    }

    public function destroy(Section $section, Andamio $andamio, Box $box)
    {

        if ($box->boxes()->exists()) {
            return redirect()->route('sections.andamios.boxes.index', ['section' => $section->id, 'andamio' => $andamio->id])
                ->withErrors('No se puede eliminar un estante con paquetes asociados.');
        }

        $box->delete();
        return redirect()->route('sections.andamios.boxes.index', ['section' => $section->id, 'andamio' => $andamio->id])
            ->with('success', 'Caja eliminada correctamente.');
    }

}
