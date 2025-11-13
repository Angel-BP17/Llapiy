<?php

namespace App\Http\Controllers\DocumentTypes;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Models\CampoType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CampoController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('admin');
            return $middleware->handle($request, $next);
        })->except('campoTypesForCreate', 'camposForEditOrShow');
    }
    public function index(Request $request)
    {
        $search = $request->input('search');
        $campos = CampoType::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%$search%");
        })->paginate(10);

        return view('campos.index', compact('campos'));
    }

    public function create()
    {
        return view('campos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:campo_types,name',
        ]);

        CampoType::create(['name' => $request->name]);

        return redirect()->route('campos.index')->with('success', 'Tipo de campo creado correctamente.');
    }

    public function edit(CampoType $campo)
    {
        return view('campos.edit', compact('campo'));
    }

    public function update(Request $request, CampoType $campo)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:campo_types,name,' . $campo->id,
        ]);

        $campo->update(['name' => $request->name]);

        return redirect()->route('campos.index')->with('success', 'Tipo de campo actualizado correctamente.');
    }

    public function destroy(CampoType $campo)
    {
        $campo->delete();

        return redirect()->route('campos.index')->with('success', 'Tipo de campo eliminado correctamente.');
    }
}
