<?php

namespace App\Http\Controllers\Inbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inbox\IndexInboxRequest;
use App\Services\Inbox\InboxService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InboxController extends Controller
{
    public function __construct(protected InboxService $service)
    {
    }

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
    public function updateStorage(Request $request, int $id, \App\Services\Block\BlockService $blockService): RedirectResponse
    {
        $request->validate([
            'n_box' => 'required|integer|exists:boxes,id',
            'n_andamio' => 'required|integer|exists:andamios,id',
            'n_section' => 'required|integer|exists:sections,id',
            'root' => 'nullable|file|mimes:pdf|max:' . (50 * 1024),
        ]);

        $this->service->updateBlockStorage($request, (int) $id);

        if ($request->hasFile('root')) {
            $block = \App\Models\Block::findOrFail($id);
            $blockService->uploadFile($block, $request->file('root'));
        }

        return redirect()->back()->with('message', 'Información de almacenamiento actualizada correctamente.');
    }
}
