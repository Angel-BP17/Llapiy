<?php

namespace App\Services\Storage;

use App\Models\Andamio;
use App\Models\Block;
use App\Models\Box;

class BoxService
{
    /**
     * Obtiene las cajas de un andamio con optimización de columnas y conteo de bloques.
     */
    public function getByAndamio(Andamio $andamio, ?string $search = null, ?int $periodo = null)
    {
        return Box::query()
            ->select(['id', 'n_box', 'andamio_id', 'created_at'])
            ->where('andamio_id', $andamio->id)
            ->withCount('blocks')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('n_box', 'like', "%{$search}%")
                        ->orWhereHas('blocks', fn ($bq) => $bq->searchStorage($search));
                });
            })
            ->when($periodo, function ($query) use ($periodo) {
                $query->whereHas('blocks', fn ($bq) => $bq->searchStorage(null, $periodo));
            })
            ->orderBy('n_box')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Obtiene bloques buscados en todo el almacén para la barra de búsqueda global.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSearchedBlocks(?string $search = null, int|string|null $periodo = null, int $limit = 5): array
    {
        return Block::getSearchedBlocks($search, $periodo, $limit);
    }

    public function create(Andamio $andamio, array $data): Box
    {
        return $andamio->boxes()->create($data);
    }

    public function update(Box $box, array $data): Box
    {
        $box->update($data);

        return $box->fresh();
    }

    public function delete(Box $box): void
    {
        $box->delete();
    }
}
