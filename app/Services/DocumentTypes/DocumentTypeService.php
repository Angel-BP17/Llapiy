<?php

namespace App\Services\DocumentTypes;

use App\Models\Area;
use App\Models\CampoDocumentType;
use App\Models\CampoType;
use App\Models\DocumentType;
use App\Models\Group;
use App\Models\Subgroup;
use Cache;
use DB;
use Illuminate\Http\Request;

class DocumentTypeService
{
    public function getIndexData(Request $request): array
    {
        $name = $request->input('name');
        $areaId = $request->input('area_id');
        $groupId = $request->input('group_id');
        $subgroupId = $request->input('subgroup_id');
        $blockDocumentTypeId = DocumentType::where('name', 'Bloque')->first()?->id;

        $query = DocumentType::query()
            ->with(['campoTypes', 'groups', 'subgroups'])
            ->withCount('documents');

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        if ($areaId) {
            $query->whereHas('groups', function ($query) use ($areaId) {
                $query->whereHas('areaGroupType', function ($query) use ($areaId) {
                    $query->where('area_id', $areaId);
                });
            });
        }

        if ($groupId) {
            $query->whereHas('groups', function ($query) use ($groupId) {
                $query->where('groups.id', $groupId);
            });
        }

        if ($subgroupId) {
            $query->whereHas('subgroups', function ($query) use ($subgroupId) {
                $query->where('subgroups.id', $subgroupId);
            });
        }

        if ($blockDocumentTypeId) {
            $query->whereNot('id', $blockDocumentTypeId);
        }

        $documentTypes = $query->paginate(10);

        $areas = Area::with('areaGroupTypes.groups.subgroups')->get();
        $groups = Group::all();
        $subgroups = Subgroup::all();

        Cache::put('areas_list', $areas, now()->addDay());
        Cache::put('groups_list', $groups, now()->addDay());
        Cache::put('subgroups_list', $subgroups, now()->addDay());

        return compact('documentTypes', 'areas', 'groups', 'subgroups');
    }

    public function getCreateData(): array
    {
        return [
            'areas' => Area::with('areaGroupTypes.groups.subgroups')->get(),
            'campoTypes' => CampoType::all(['id', 'name']),
            'groups' => Group::all(),
            'subgroups' => Subgroup::all(),
        ];
    }

    public function create(Request $request): void
    {
        DB::beginTransaction();

        $documentType = DocumentType::create([
            'name' => $request->name,
        ]);

        $campoTypeIds = json_decode($request->campos, true) ?? [];

        $groupIds = json_decode($request->groups, true) ?? [];
        if (!empty($groupIds)) {
            $documentType->groups()->sync($groupIds);
        }

        $subgroupIds = json_decode($request->subgroups, true) ?? [];
        if (!empty($subgroupIds)) {
            $documentType->subgroups()->sync($subgroupIds);
        }

        if (!empty($campoTypeIds)) {
            foreach ($campoTypeIds as $campoTypeId) {
                CampoDocumentType::create([
                    'document_type_id' => $documentType->id,
                    'campo_type_id' => $campoTypeId,
                ]);
            }
        }

        DB::commit();
    }

    public function getEditData(DocumentType $documentType): array
    {
        return [
            'documentType' => $documentType->load(['groups', 'subgroups', 'campoTypes']),
            'campoTypes' => CampoType::all(['id', 'name']),
            'areas' => Area::with('areaGroupTypes.groups.subgroups.subgroups')->get(),
        ];
    }

    public function update(Request $request, DocumentType $documentType): void
    {
        DB::beginTransaction();

        $documentType->update(['name' => $request->name]);

        $campoTypeIds = json_decode($request->campos, true);
        $campoTypeIds = is_array($campoTypeIds) ? $campoTypeIds : [];
        $validCampoIds = CampoType::whereIn('id', $campoTypeIds)->pluck('id')->all();
        $documentType->campoTypes()->sync($validCampoIds);

        $groupIds = json_decode($request->groups, true);
        $groupIds = is_array($groupIds) ? $groupIds : [];
        $validGroupIds = Group::whereIn('id', $groupIds)->pluck('id')->all();
        $documentType->groups()->sync($validGroupIds);

        $subgroupIds = json_decode($request->subgroups, true);
        $subgroupIds = is_array($subgroupIds) ? $subgroupIds : [];
        $validSubgroupIds = Subgroup::whereIn('id', $subgroupIds)->pluck('id')->all();
        $documentType->subgroups()->sync($validSubgroupIds);

        DB::commit();
    }

    public function delete(DocumentType $documentType): void
    {
        CampoDocumentType::where('document_type_id', $documentType->id)->delete();
        $documentType->delete();
    }
}
