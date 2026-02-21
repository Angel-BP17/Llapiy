<?php

namespace App\Services\Areas;

use App\Models\GroupType;

class GroupTypeService
{
    public function getAll(?string $search = null)
    {
        return GroupType::query()
            ->with(['areaGroupTypes.groups'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('descripcion', 'like', "%{$search}%")
                        ->orWhere('abreviacion', 'like', "%{$search}%");
                });
            })
            ->orderBy('descripcion')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data): void
    {
        GroupType::create($data);
    }

    public function find(int $id): GroupType
    {
        return GroupType::findOrFail($id);
    }

    public function update(GroupType $groupType, array $data): void
    {
        $groupType->update($data);
    }

    public function delete(GroupType $groupType): void
    {
        $groupType->delete();
    }
}
