<?php

namespace App\Services\Storage;

use App\Models\Section;

class SectionService
{
    public function getAll(?string $search = null)
    {
        return Section::query()
            ->withCount('andamios')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('n_section', 'like', "%{$search}%")
                        ->orWhere('descripcion', 'like', "%{$search}%");
                });
            })
            ->get();
    }

    public function create(array $data): void
    {
        Section::create($data);
    }

    public function update(Section $section, array $data): void
    {
        $section->update($data);
    }

    public function delete(Section $section): void
    {
        $section->delete();
    }
}
