<?php

namespace App\Services\Storage;

use App\Models\Andamio;
use App\Models\Block;
use App\Models\Section;

class AndamioService
{
    /**
     * Obtiene los andamios de una sección específica con optimización de columnas.
     */
    public function getBySection(Section $section, ?string $search = null, ?int $periodo = null)
    {
        return $section->andamios()
            ->select(['id', 'n_andamio', 'descripcion', 'section_id', 'created_at'])
            ->withCount('boxes')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('n_andamio', 'like', "%{$search}%")
                        ->orWhere('descripcion', 'like', "%{$search}%")
                        ->orWhereHas('boxes.blocks', fn ($bq) => $bq->searchStorage($search));
                });
            })
            ->when($periodo, function ($query) use ($periodo) {
                $query->whereHas('boxes.blocks', fn ($bq) => $bq->searchStorage(null, $periodo));
            })
            ->orderBy('n_andamio')
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

    public function create(Section $section, array $data): Andamio
    {
        return $section->andamios()->create($data);
    }

    public function update(Andamio $andamio, array $data): Andamio
    {
        $andamio->update($data);

        return $andamio->fresh();
    }

    public function delete(Andamio $andamio): void
    {
        $andamio->delete();
    }
}
