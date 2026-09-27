<?php

namespace App\Services\Storage;

use App\Models\Andamio;
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
                        ->orWhereHas('blocks', function ($q) use ($search) {
                            $q->where('n_bloque', 'like', "%{$search}%")
                                ->orWhere('asunto', 'like', "%{$search}%")
                                ->orWhere('periodo', 'like', "%{$search}%")
                                ->orWhereHas('periods', fn ($p) => $p->where('periodo', 'like', "%{$search}%"));
                        });
                });
            })
            ->when($periodo, function ($query) use ($periodo) {
                $query->whereHas('blocks', function ($q) use ($periodo) {
                    $q->where(function ($sub) use ($periodo) {
                        $sub->where('periodo', $periodo)
                            ->orWhereYear('fecha', $periodo)
                            ->orWhereHas('periods', fn ($p) => $p->where('periodo', $periodo));
                    });
                });
            })
            ->orderBy('n_box')
            ->paginate(10)
            ->withQueryString();
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
