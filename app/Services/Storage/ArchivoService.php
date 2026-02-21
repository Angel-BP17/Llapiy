<?php

namespace App\Services\Storage;

use App\Models\Block;
use App\Models\Box;

class ArchivoService
{
    public function getBoxWithBlocks(int $boxId, ?string $search = null): array
    {
        $box = Box::query()->findOrFail($boxId);

        $blocks = $box->blocks()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('n_bloque', 'like', "%{$search}%")
                        ->orWhere('asunto', 'like', "%{$search}%")
                        ->orWhere('folios', 'like', "%{$search}%")
                        ->orWhere('periodo', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return compact('box', 'blocks');
    }

    public function moveToDefault(int $boxId, int $blockId): void
    {
        $block = Block::findOrFail($blockId);

        if ($block->box_id != $boxId) {
            throw new \RuntimeException('El archivo no pertenece ala caja especificada.');
        }

        $block->update(['box_id' => null]);
    }
}
