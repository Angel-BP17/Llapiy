<?php

namespace App\Http\Controllers\Storage;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Models\Block;
use App\Models\Box;
use App\Http\Controllers\Controller;

class ArchivoController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('encargado');
            return $middleware->handle($request, $next);
        });
    }
    public function index($section, $andamio, $box)
    {

        // Obtener el paquete con sus archivos relacionados
        $box = Box::with('blocks')->findOrFail($box);

        // Retornar la vista con los archivos
        return view('archivos.index', compact('section', 'andamio', 'box'));
    }

    /**
     * Mueve un archivo al contenedor default.
     */
    public function moveToDefault($section, $andamio, $box, $block)
    {

        // Obtener el archivo
        $block = Block::findOrFail($block);

        // Verificar que el archivo pertenece al paquete especificado
        if ($block->box_id != $box) {
            return back()->with('error', 'El archivo no pertenece ala caja especificada.');
        }

        // Asignar el archivo al contenedor default
        $block->update(['box_id' => null]); // Null representa el contenedor default

        // Redirigir con éxito
        return back()->with('success', 'Archivo movido al contenedor default.');
    }
}
