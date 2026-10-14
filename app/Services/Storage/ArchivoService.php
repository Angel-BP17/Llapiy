<?php

namespace App\Services\Storage;

use App\Models\Block;
use App\Models\Box;

class ArchivoService
{
    /**
     * Obtiene una caja y sus bloques asociados con búsqueda y paginación.
     */
    public function getBoxWithBlocks(int $boxId, ?string $search = null, ?int $periodo = null): array
    {
        $box = Box::findOrFail($boxId);

        $blocks = $box->blocks()
            ->select([
                'id',
                'n_bloque',
                'asunto',
                'folios',
                'periodo',
                'fecha',
                'rango_inicial',
                'rango_final',
                'box_id',
                'root',
                'created_at',
                'documentary_series_id',
                'user_id',
                'group_id',
                'subgroup_id',
            ])
            ->with(['documentarySeries:id,codigo,nombre', 'periods'])
            ->searchStorage($search, $periodo)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return compact('box', 'blocks');
    }

    /**
     * Mueve un bloque fuera de una caja (lo devuelve al estado pendiente/general).
     */
    public function moveToDefault(int $boxId, int $blockId): void
    {
        $block = Block::findOrFail($blockId);

        if ((int) $block->box_id !== $boxId) {
            throw new \RuntimeException('El archivo no pertenece a la caja especificada.');
        }

        $block->update(['box_id' => null]);
    }
}
