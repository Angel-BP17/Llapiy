<?php

namespace App\Services\Storage;

use App\Models\Andamio;
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
                        ->orWhereHas('boxes.blocks', function ($q) use ($search) {
                            $q->where('n_bloque', 'like', "%{$search}%")
                                ->orWhere('asunto', 'like', "%{$search}%")
                                ->orWhere('periodo', 'like', "%{$search}%")
                                ->orWhereHas('periods', fn ($p) => $p->where('periodo', 'like', "%{$search}%"));
                        });
                });
            })
            ->when($periodo, function ($query) use ($periodo) {
                $query->whereHas('boxes.blocks', function ($q) use ($periodo) {
                    $q->where(function ($sub) use ($periodo) {
                        $sub->where('periodo', $periodo)
                            ->orWhereYear('fecha', $periodo)
                            ->orWhereHas('periods', fn ($p) => $p->where('periodo', $periodo));
                    });
                });
            })
            ->orderBy('n_andamio')
            ->paginate(10)
            ->withQueryString();
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
