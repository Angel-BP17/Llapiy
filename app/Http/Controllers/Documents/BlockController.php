<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\CreateBlockRequest;
use App\Http\Requests\Document\IndexBlockRequest;
use App\Http\Requests\Document\UpdateBlockRequest;
use App\Http\Requests\Document\UploadBlockFileRequest;
use App\Models\Area;
use App\Models\Block;
use App\Models\Document;
use App\Models\User;
use App\Notifications\NewBlockNotification;
use App\Services\Block\BlockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BlockController extends Controller
{
    public function __construct(protected BlockService $service)
    {
    }

    public function index(IndexBlockRequest $request)
    {
        $resources = $this->service->getAll($request);
        $totalBlocks = Block::count();
        $attendedBlocksCount = Block::query()
            ->whereNotNull('root')
            ->where('root', '!=', '')
            ->whereNotNull('box_id')
            ->whereHas('box.andamio')
            ->count();
        $unattendedBlocksCount = max($totalBlocks - $attendedBlocksCount, 0);

        return $this->apiSuccess('Bloques obtenidos correctamente.', [
            'blocks' => $resources['blocks']->paginate(10),
            'areas' => $resources['areas'],
            'groups' => $resources['groups'],
            'subgroups' => $resources['subgroups'],
            'years' => $resources['years'],
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'totalBlocks' => $totalBlocks,
            'totalAreas' => Area::count(),
            'attendedBlocksCount' => $attendedBlocksCount,
            'unattendedBlocksCount' => $unattendedBlocksCount,
        ]);
    }

    public function store(CreateBlockRequest $request)
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

            $this->clearDocumentCache();

            return $this->apiSuccess('Bloque creado correctamente.', ['block' => $block], 201);
        } catch (\Throwable $e) {
            Log::error('Error al registrar el bloque: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->apiError('Ocurrio un error al registrar el bloque. Intenta nuevamente.', 500);
        }
    }

    public function update(UpdateBlockRequest $request, Block $block)
    {
        try {
            $updated = $this->service->update($request->validated(), $request->file('root'), $request->hasFile('root'), $block);

            $this->clearDocumentCache();

            return $this->apiSuccess('Bloque actualizado correctamente.', ['block' => $updated]);
        } catch (\Throwable $e) {
            Log::error('Error al editar el bloque: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->apiError('Ocurrio un error al editar el bloque. Intenta nuevamente.', 500);
        }
    }

    public function uploadFile(UploadBlockFileRequest $request, Block $block)
    {
        try {
            $updated = $this->service->uploadFile($block, $request->file('root'));
            $this->clearDocumentCache();

            return $this->apiSuccess('Archivo del bloque actualizado correctamente.', ['block' => $updated]);
        } catch (\Throwable $e) {
            Log::error('Error al subir archivo del bloque: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->apiError('Ocurrio un error al subir el archivo del bloque. Intenta nuevamente.', 500);
        }
    }

    public function destroy(Block $block)
    {
        try {
            $this->service->delete($block);
            $this->clearDocumentCache();

            return $this->apiSuccess('Bloque eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar el bloque: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->apiError('Ocurrio un error al eliminar el bloque. Intenta nuevamente.', 500);
        }
    }

    public function generatePDFReport(Request $request)
    {
        $blocks = $this->service->report($request)->with('box.andamio.section')->get();

        return Pdf::loadView('blocks.report', compact('blocks'))
            ->setPaper('a4', 'landscape')
            ->stream('reporte_bloques.pdf');
    }

    protected function clearDocumentCache(?Document $document = null): void
    {
        $keys = [
            'admin_block_count',
            'document_types_list',
            'areas_list',
        ];

        if ($document) {
            $keys[] = 'document_' . $document->id;
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
