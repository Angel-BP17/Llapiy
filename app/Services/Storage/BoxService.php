<?php

namespace App\Services\Storage;

use App\Models\Andamio;
use App\Models\Box;

class BoxService
{
    public function getByAndamio(Andamio $andamio, ?string $search = null)
    {
        return Box::query()
            ->where('andamio_id', $andamio->id)
            ->withCount('blocks')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('n_box', 'like', "%{$search}%");
                });
            })
            ->get();
    }

    public function create(Andamio $andamio, array $data): void
    {
        $andamio->boxes()->create($data);
    }

    public function update(Box $box, array $data): void
    {
        $box->update($data);
    }

    public function delete(Box $box): void
    {
        $box->delete();
    }
}
