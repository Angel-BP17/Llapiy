<?php

namespace App\Services\Storage;

use App\Models\Andamio;
use App\Models\Section;

class AndamioService
{
    /**
     * Obtiene los andamios de una sección específica con optimización de columnas.
     */
    public function getBySection(Section $section, ?string $search = null)
    {
        return $section->andamios()
            ->select(['id', 'n_andamio', 'descripcion', 'section_id', 'created_at'])
            ->withCount('boxes')
            ->when($search, function ($query) use ($search) {
                $query->where('n_andamio', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%");
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
