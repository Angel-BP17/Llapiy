<?php

namespace App\Http\Controllers\Documents;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Http\Requests\Document\CreateBlockRequest;
use App\Http\Requests\Document\UpdateBlockRequest;
use App\Models\Area;
use App\Models\Block;
use App\Models\Document;
use App\Models\User;
use App\Notifications\NewBlockNotification;
use App\Services\Block\BlockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Cache;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class BlockController extends Controller
{
    public function __construct(protected BlockService $service)
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('user');
            return $middleware->handle($request, $next);
        });

    }
    public function index(Request $request)
    {
        $resources = $this->service->getAll($request);

        return view('blocks.index', [
            'blocks' => $resources['blocks']->paginate(10),
            'areas' => $resources['areas'],
            'groups' => $resources['groups'],
            'subgroups' => $resources['subgroups'],
            'years' => $resources['years']
        ]);
    }

    public function create()
    {
        return view('blocks.create', [
            'areas' => Area::all(),
        ]);
    }

    public function store(CreateBlockRequest $request)
    {
        try {
            $block = $this->service->create($request->validated(), $request->file('root'));

            retry(3, function () use ($block) {
                $this->notifyManagers($block);
            }, 100);

            $this->clearDocumentCache();

            return redirect()->route('blocks.index')->with('success', 'Documento subido exitosamente');
        } catch (\Throwable $e) {
            Log::error('Error al registrar el bloque: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withErrors('Ocurrió un error al registrar el documento. Intenta nuevamente.')
                ->withInput();
        }
    }

    public function show(Block $block)
    {
        Carbon::setLocale('es');
        $fecha = $block->fecha;

        return view('blocks.show', [
            'block' => $block,
            'year' => $fecha->year,
            'month' => $fecha->translatedFormat('F'),
            'day' => $fecha->day,
            'daySem' => $fecha->translatedFormat('l'),
        ]);
    }

    public function edit(Block $block)
    {
        return view('blocks.edit', [
            'block' => $block,
        ]);
    }

    public function update(UpdateBlockRequest $request, Block $block)
    {
        try {
            $newBlock = $this->service->update($request->validated(), $request->file('root'), $request->hasFile('root'), $block);

            retry(3, function () use ($newBlock) {
                $this->notifyManagers($newBlock, true);
            }, 100);

            $this->clearDocumentCache();

            return redirect()->route('blocks.index')->with('success', 'Bloque actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al editar el bloque: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withErrors('Ocurrió un error al editar el bloque. Intenta nuevamente.')
                ->withInput();
        }

    }

    public function destroy(Block $block)
    {
        try {
            $this->service->delete($block);
            $this->clearDocumentCache();
            return redirect()->route('blocks.index')->with('success', 'Documento eliminado exitosamente');
        } catch (\Throwable $e) {
            Log::error('Error al editar el bloque: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withErrors('Ocurrió un error al eliminar el documento. Intenta nuevamente.')
                ->withInput();
        }
    }

    public function generatePDFReport(Request $request)
    {
        $blocks = $this->service->report($request)->with('box.andamio.section')->get();

        return Pdf::loadView('blocks.report', compact('blocks'))
            ->setPaper('a4', 'landscape')
            ->stream('reporte_bloques.pdf');
    }

    protected function clearDocumentCache(Document $document = null)
    {
        $keys = [
            'admin_block_count',
            'document_types_list',
            'areas_list'
        ];

        if ($document) {
            $keys[] = 'document_' . $document->id;
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    private function notifyManagers(Block $block, bool $isCreate = true)
    {
        if ($isCreate) {
            // Implementación con chunk para evitar memory issues
            User::whereHas('userType', fn($q) => $q->where('name', 'Revisor/Aprobador'))
                ->chunkById(100, function ($users) use ($block) {
                    foreach ($users as $user) {
                        $user->notify(new NewBlockNotification($block));
                    }
                });
        }
    }
}
