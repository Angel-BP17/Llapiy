<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\Block\IndexBlockRequest;
use App\Http\Requests\Block\CreateBlockRequest;
use App\Http\Requests\Block\UpdateBlockRequest;
use App\Http\Requests\Block\UploadBlockFileRequest;
use App\Models\Block;
use App\Models\User;
use App\Notifications\NewBlockNotification;
use App\Services\Block\BlockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class BlockController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected BlockService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexBlockRequest $request): Response
    {
        $resources = $this->service->getAll($request);
        $query = $resources['blocks'];

        $totalBlocks = (clone $query)->count();
        $attendedBlocksCount = (clone $query)
            ->whereNotNull('root')
            ->where('root', '!=', '')
            ->whereNotNull('box_id')
            ->whereHas('box.andamio')
            ->count();
        $unattendedBlocksCount = max($totalBlocks - $attendedBlocksCount, 0);

        $paginatedBlocks = $query->paginate(10);

        $paginatedBlocks->getCollection()->transform(function ($block) {
            $block->load(['user', 'group.areaGroupType.area', 'subgroup', 'box.andamio.section', 'documentarySeries']);
            
            $block->area = $block->group?->areaGroupType?->area?->descripcion ?? 'Sin área';
            $block->group_name = $block->group?->descripcion ?? 'Sin grupo';
            $block->subgroup_name = $block->subgroup?->descripcion ?? 'Sin subgrupo';
            
            if ($block->box) {
                $block->box_info = [
                    'section' => $block->box->andamio?->section?->n_section ?? '-',
                    'andamio' => $block->box->andamio?->n_andamio ?? '-',
                    'box' => $block->box->n_box ?? '-',
                    'paquete' => $block->box->paquete ?? '-',
                ];
            }

            $block->can = [
                'update' => auth()->user()->can('update', $block),
                'delete' => auth()->user()->can('delete', $block),
                'view' => auth()->user()->can('view', $block),
            ];
            return $block;
        });

        return Inertia::render('blocks/index', [
            'blocks' => $paginatedBlocks->items(),
            'areas' => $resources['areas'],
            'groups' => $resources['groups'],
            'subgroups' => $resources['subgroups'],
            'years' => $resources['years'],
            'sections' => $resources['sections'],
            'andamios' => $resources['andamios'],
            'boxes' => $resources['boxes'],
            'documentarySeries' => $resources['documentarySeries'],
            'stats' => [
                'totalBlocks' => $totalBlocks,
                'attendedCount' => $attendedBlocksCount,
                'unattendedCount' => $unattendedBlocksCount,
            ],
            'pagination' => [
                'total' => $paginatedBlocks->total(),
                'current_page' => $paginatedBlocks->currentPage(),
                'last_page' => $paginatedBlocks->lastPage(),
                'from' => $paginatedBlocks->firstItem(),
                'to' => $paginatedBlocks->lastItem(),
            ],
            'filters' => $request->only(['asunto', 'n_bloque', 'area_id', 'group_id', 'subgroup_id', 'year', 'month', 'section_id', 'andamio_id', 'box_id']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBlockRequest $request): RedirectResponse
    {
        try {
            $block = $this->service->create($request->validated(), $request->file('root'));

            $notificationPermission = 'notifications.receive';
            if (Permission::query()->where('name', $notificationPermission)->exists()) {
                $receivers = User::permission($notificationPermission)->get();
                if ($receivers->isNotEmpty()) {
                    Notification::send($receivers, new NewBlockNotification($block));
                }
            }

            return redirect()->back()->with('message', 'Bloque creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar el bloque: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrio un error al registrar el bloque.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Block $block): Response
    {
        $this->authorize('view', $block);
        $block->load([
            'user',
            'group.areaGroupType.area',
            'subgroup',
            'box.andamio.section',
            'documentarySeries'
        ]);
        
        return Inertia::render('blocks/show', [
            'block' => $block
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlockRequest $request, Block $block): RedirectResponse
    {
        try {
            $this->authorize('update', $block);
            $this->service->update($request->validated(), $request->file('root'), $request->hasFile('root'), $block);

            return redirect()->back()->with('message', 'Bloque actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al editar el bloque: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrio un error al editar el bloque.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Block $block): RedirectResponse
    {
        try {
            $this->authorize('delete', $block);
            $this->service->delete($block);

            return redirect()->back()->with('message', 'Bloque eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar el bloque: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrio un error al eliminar el bloque.');
        }
    }

    /**
     * View/Download the block file.
     */
    public function file(Block $block)
    {
        if (!$block->root) {
            abort(404, 'El bloque no tiene un archivo adjunto.');
        }

        if (!Storage::disk('public')->exists($block->root)) {
            abort(404, 'El archivo no existe en el almacenamiento.');
        }

        return Storage::disk('public')->response($block->root);
    }

    /**
     * Upload or update the block file.
     */
    public function upload(UploadBlockFileRequest $request, Block $block): RedirectResponse
    {
        try {
            $this->authorize('update', $block);
            $this->service->uploadFile($block, $request->file('root'));

            return redirect()->back()->with('message', 'Archivo del bloque actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al subir archivo del bloque: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrio un error al subir el archivo del bloque.');
        }
    }

    /**
     * Generate PDF report.
     */
    public function pdf(Request $request)
    {
        try {
            $blocks = $this->service->report($request)->with('box.andamio.section')->get();

            return Pdf::loadView('blocks.report', compact('blocks'))
                ->setPaper('a4', 'landscape')
                ->stream('reporte_bloques.pdf');
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo generar el reporte de bloques.'], 500);
        }
    }
}
