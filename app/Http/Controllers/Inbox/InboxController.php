<?php

namespace App\Http\Controllers\Inbox;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Models\Andamio;
use App\Models\Area;
use App\Models\Block;
use App\Models\Box;
use App\Models\Section;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('encargado');
            return $middleware->handle($request, $next);
        })->except(['getAndamios', 'getCajas']);
    }
    public function index(Request $request)
    {
        $query = Block::withoutBox();

        if ($request->has('search') && !empty($request->search)) {
            $query->where('asunto', 'like', '%' . $request->search . '%');
        }

        if ($request->has('area_id') && !empty($request->area_id)) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->has('fecha') && !empty($request->fecha)) {
            $query->where('fecha', $request->fecha);
        }

        $documents = $query->latest()->paginate(10);

        // Para el formulario de filtro
        $areas = Area::all();
        $fechas = Block::select('fecha')->distinct()->pluck('fecha');

        $periodos = $fechas->map(function ($fecha) {
            return \Carbon\Carbon::parse($fecha)->year;
        })->unique()->values();

        $sections = Section::all();
        $andamios = Andamio::all();
        $boxes = Box::all();

        return view('inbox.index', compact('documents', 'areas', 'fechas', 'periodos', 'sections', 'andamios', 'boxes'));
    }

    public function updateBlockStorage(Request $request, $id)
    {
        $request->validate([
            'n_box' => 'required|integer',
            'n_andamio' => 'required|string',
            'n_section' => 'required|string',
        ]);

        $document = Block::findOrFail($id);

        // Actualiza los valores relacionados con almacenamiento
        $document->box_id = $request->n_box; // Asociar paquete
        $document->save();

        // Actualizar las relaciones en cascada, si es necesario
        // Aquí asumimos que las demás entidades están relacionadas y podemos asignarlas según las llaves foráneas
        return redirect()->route('inbox.index')->with('success', 'Información de almacenamiento actualizada correctamente.');
    }
}
