<?php

namespace App\Services\Storage;

use App\Models\Andamio;
use App\Models\Section;

class AndamioService
{
    public function getBySection(Section $section, ?string $search = null)
    {
        return $section->andamios()
            ->withCount('boxes')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('n_andamio', 'like', "%{$search}%")
                        ->orWhere('descripcion', 'like', "%{$search}%");
                });
            })
            ->get();
    }

    public function create(Section $section, array $data): void
    {
        $section->andamios()->create($data);
    }

    public function update(Andamio $andamio, array $data): void
    {
        $andamio->update($data);
    }

    public function delete(Andamio $andamio): void
    {
        $andamio->delete();
    }
}
