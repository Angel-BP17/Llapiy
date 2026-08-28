<?php

namespace App\Services\Storage;

use App\Models\Section;

class SectionService
{
    /**
     * Obtiene todas las secciones con conteo de andamios, optimizado para listado.
     */
    public function getAll(?string $search = null)
    {
        return Section::query()
            ->select(['id', 'n_section', 'descripcion', 'created_at'])
            ->withCount('andamios')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('n_section', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%")
                      ->orWhereHas('andamios.boxes.blocks', function ($q) use ($search) {
                          $q->where('n_bloque', 'like', "%{$search}%")
                            ->orWhere('asunto', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('n_section')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data): Section
    {
        return Section::create($data);
    }

    public function update(Section $section, array $data): Section
    {
        $section->update($data);
        return $section->fresh();
    }

    public function delete(Section $section): void
    {
        $section->delete();
    }
}
