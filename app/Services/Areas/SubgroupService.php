<?php

namespace App\Services\Areas;

use App\Models\DocumentType;
use App\Models\Subgroup;
use App\Models\SubgroupDocumentType;
use Illuminate\Http\Request;

class SubgroupService
{
    public function create(Request $request): void
    {
        $subgroup = Subgroup::create([
            'group_id' => $request->group_id,
            'descripcion' => $request->descripcion ?? 'Nuevo Subgrupo',
            'abreviacion' => $request->abreviacion,
            'parent_subgroup_id' => $request->parent_subgroup_id,
        ]);

        $bloque = DocumentType::where('name', 'Bloque')->first();

        SubgroupDocumentType::create([
            'subgroup_id' => $subgroup->id,
            'document_type_id' => $bloque->id,
        ]);
    }

    public function find(int $id): Subgroup
    {
        return Subgroup::findOrFail($id);
    }

    public function update(Subgroup $subgroup, array $data): void
    {
        $subgroup->update($data);
    }

    public function delete(Subgroup $subgroup): void
    {
        $subgroup->delete();
    }
}

