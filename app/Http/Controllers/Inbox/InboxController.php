<?php

namespace App\Http\Controllers\Inbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inbox\IndexInboxRequest;
use App\Services\Inbox\InboxService;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function __construct(protected InboxService $service)
    {
    }

    public function index(IndexInboxRequest $request)
    {
        return $this->apiSuccess('Bandeja obtenida correctamente.', $this->service->getIndexData($request));
    }

    public function updateBlockStorage(Request $request, int $id)
    {
        $validated = $request->validate([
            'n_box' => 'required|integer|exists:boxes,id',
            'n_andamio' => 'required|integer|exists:andamios,id',
            'n_section' => 'required|integer|exists:sections,id',
        ]);

        $this->service->updateBlockStorage($request, (int) $id);

        return $this->apiSuccess('Informacion de almacenamiento actualizada correctamente.', [
            'id' => (int) $id,
            'storage' => $validated,
        ]);
    }
}
