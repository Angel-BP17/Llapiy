<?php

namespace App\Http\Controllers\Inbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inbox\IndexInboxRequest;
use App\Http\Requests\Inbox\UpdateStorageInboxRequest;
use App\Services\Inbox\InboxService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InboxController extends Controller
{
    public function __construct(protected InboxService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexInboxRequest $request): Response
    {
        return Inertia::render('inbox/index', $this->service->getIndexData($request));
    }

    /**
     * Update storage information for a block.
     */
    public function updateStorage(UpdateStorageInboxRequest $request, int $id, \App\Services\Block\BlockService $blockService): RedirectResponse
    {
        $this->service->updateBlockStorage($request, (int) $id);

        if ($request->hasFile('root')) {
            $block = \App\Models\Block::findOrFail($id);
            $blockService->uploadFile($block, $request->file('root'));
        }

        return redirect()->back()->with('message', 'Información de almacenamiento actualizada correctamente.');
    }

    /**
     * Delete digital file attached to a block.
     */
    public function deleteFile(int $id): RedirectResponse
    {
        $this->service->deleteBlockFile((int) $id);

        return redirect()->back()->with('message', 'Archivo del documento eliminado correctamente.');
    }
}
