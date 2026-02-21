<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storage\IndexArchivoRequest;
use App\Services\Storage\ArchivoService;

class ArchivoController extends Controller
{
    public function __construct(protected ArchivoService $service)
    {
    }

    public function index(IndexArchivoRequest $request, $section, $andamio, $box)
    {
        $resources = $this->service->getBoxWithBlocks((int) $box, $request->input('search'));

        return $this->apiSuccess('Archivos obtenidos correctamente.', [
            'section' => (int) $section,
            'andamio' => (int) $andamio,
            'box' => $resources['box'],
            'blocks' => $resources['blocks'],
        ]);
    }

    public function moveToDefault($section, $andamio, $box, $block)
    {
        try {
            $this->service->moveToDefault((int) $box, (int) $block);

            return $this->apiSuccess('Archivo movido al contenedor default.');
        } catch (\RuntimeException $e) {
            return $this->apiError($e->getMessage(), 422);
        }
    }
}
