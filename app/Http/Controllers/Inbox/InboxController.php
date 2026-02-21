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

    public function updateBlockStorage(Request $request, $id)
    {
        $validated = $request->validate([
            'n_box' => 'required|integer',
            'n_andamio' => 'required|string',
            'n_section' => 'required|string',
        ]);

        $this->service->updateBlockStorage($request, (int) $id);

        return $this->apiSuccess('Informacion de almacenamiento actualizada correctamente.', [
            'id' => (int) $id,
            'storage' => $validated,
        ]);
    }
}
