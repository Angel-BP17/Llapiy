<?php

namespace App\Services\Areas;

use App\Models\Subgroup;
use Illuminate\Http\Request;

class SubgroupService
{
    /**
     * Crea un nuevo subgrupo dentro de un grupo organizacional.
     */
    public function create(Request $request): Subgroup
    {
        return Subgroup::create([
            'group_id' => $request->group_id,
            'descripcion' => $request->descripcion ?? 'Nuevo Subgrupo',
            'abreviacion' => $request->abreviacion ?? strtoupper(substr($request->descripcion ?? 'SUB', 0, 3)),
            'parent_subgroup_id' => $request->parent_subgroup_id,
        ]);
    }

    public function find(int $id): Subgroup
    {
        return Subgroup::findOrFail($id);
    }

    public function update(Subgroup $subgroup, array $data): Subgroup
    {
        $subgroup->update($data);
        return $subgroup->fresh();
    }

    public function delete(Subgroup $subgroup): void
    {
        $subgroup->delete();
    }
}
