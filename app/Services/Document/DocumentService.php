<?php

namespace App\Services\Document;

use App\Models\Area;
use App\Models\Campo;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Group;
use App\Models\Subgroup;
use Auth;
use Carbon\Carbon;
use DB;
use Storage;
use Str;

class DocumentService
{
    public function getAll($data)
    {
        $query = Document::query()->with(['documentType.groups.areaGroupType.area', 'documentType.subgroups', 'user'])
            ->when(
                !Auth::user()->isAdminOrManager(),
                fn($q) => $q->when('group_id', fn($q) => $q->when('sugroup_id', fn($q) => $q->where('group_id', Auth::user()->group_id)))->when('subgroup_id', fn($q) => $q->where('subgroup_id', Auth::user()->subgroup_id))
            )
            ->when(
                $data->asunto,
                fn($q, $asunto) => $q->where('asunto', 'like', "%{$asunto}%")
            )
            ->when(
                $data->document_type_id,
                fn($q, $typeId) => $q->where('document_type_id', $typeId)
            )
            ->when(
                $data->area_id,
                fn($q, $areaId) => $q->whereHas('documentType.groups.areaGroupType.area', function ($q) use ($areaId) {
                    $q->where('id', $areaId);
                })
            )
            ->when(
                $data->group_id,
                fn($q, $groupId) => $q->whereHas('documentType.groups', function ($q) use ($groupId) {
                    $q->where('groups.id', $groupId);
                })
            )
            ->when(
                $data->subgroup_id,
                fn($q, $subgroupId) => $q->whereHas('documentType.subgroups', function ($q) use ($subgroupId) {
                    $q->where('subgroups.id', $subgroupId);
                })
            )
            ->when(
                $data->year,
                fn($q, $year) => $q->whereYear('fecha', $year)
            )
            ->when(
                $data->month,
                fn($q, $month) => $q->whereMonth('fecha', $month)
            );

        $years = Document::selectRaw('YEAR(fecha) as year')->distinct()->pluck('year');

        return [
            'documents' => $query,
            'areas' => Area::with('areaGroupTypes.groups.subgroups')->get(),
            'groups' => Group::all(),
            'documentTypes' => $this->userDocumentTypes(),
            'subgroups' => Subgroup::all(),
            'years' => $years,
        ];
    }

    public function create(array $data, $file, $inputs)
    {
        return DB::transaction(function () use ($data, $file, $inputs) {
            $filePath = $this->storeDocumentFile($file, $data['asunto']);

            $document = Document::create([
                'n_documento' => $data['n_documento'],
                'asunto' => $data['asunto'],
                'root' => $filePath,
                'folios' => $data['folios'],
                'document_type_id' => $data['document_type_id'],
                'user_id' => Auth::id(),
                'fecha' => $data['fecha'],
                'periodo' => Carbon::parse($data['fecha'])->year,
                'group_id' => Auth::user()->group_id,
                'subgroup_id' => Auth::user()->subgroup_id,
            ]);

            $this->storeDocumentFields($document, $inputs ?? []);

            return $document;
        });
    }

    public function update(array $data, $model, $file, $hasFile, $inputs)
    {
        return DB::transaction(function () use ($data, $model, $file, $hasFile, $inputs) {
            // Bloqueo pesimista para el documento
            $document = Document::lockForUpdate()->findOrFail($model->id);

            if ($hasFile) {
                Storage::delete("public/{$data['root']}");
                $data['root'] = $this->storeDocumentFile($file, $data['asunto']);
            }

            $document->update([
                'n_bloque' => $data['n_bloque'] ?? $document->n_bloque,
                'asunto' => $data['asunto'] ?? $document->asunto,
                'folios' => $data['folios'] ?? $document->folios,
                'fecha' => $data['fecha'] ?? $document->fecha,
                'root' => $data['root'] ?? $document->root,
                'periodo' => Carbon::parse($data['fecha'])->year ?? $document->periodo,
                'group_id' => Auth::user()->group_id,
                'subgroup' => Auth::user()->subgroup_id,
            ]);

            $this->updateDocumentFields($document, $inputs ?? []);

            return $document;
        });
    }

    public function delete($model)
    {
        return DB::transaction(function () use ($model) {
            $document = Document::lockForUpdate()->findOrFail($model->id);
            $filePath = $document->root;
            $document->delete();
            Storage::delete("public/{$filePath}");
        });
    }

    //SERVICIOS PERSONALIZADOS

    //PÚBLICOS

    public function report($data)
    {
        return Document::query()->with(['group.areaGroupType.area', 'subgroup', 'user'])
            ->when(
                !Auth::user()->isAdminOrManager(),
                fn($q) => $q->when('group_id', fn($q) => $q->when('sugroup_id', fn($q) => $q->where('group_id', Auth::user()->group_id)))->when('subgroup_id', fn($q) => $q->where('subgroup_id', Auth::user()->subgroup_id))
            )
            ->when(
                $data->asunto,
                fn($q, $asunto) => $q->where('asunto', 'like', "%{$asunto}%")
            )
            ->when(
                $data->area_id,
                fn($q, $areaId) => $q->whereHas('group.areaGroupType.area', function ($q) use ($areaId) {
                    $q->where('id', $areaId);
                })
            )
            ->when(
                $data->group_id,
                fn($q, $groupId) => $q->whereHas('documentType.groups', function ($q) use ($groupId) {
                    $q->where('groups.id', $groupId);
                })
            )
            ->when(
                $data->subgroup_id,
                fn($q, $subgroupId) => $q->whereHas('documentType.subgroups', function ($q) use ($subgroupId) {
                    $q->where('subgroups.id', $subgroupId);
                })
            )
            ->when(
                $data->year,
                fn($q, $year) => $q->whereYear('fecha', $year)
            )
            ->when(
                $data->month,
                fn($q, $month) => $q->whereMonth('fecha', $month)
            );
    }

    public function userDocumentTypesWithCampos($user)
    {
        // Verifica que el usuario tenga grupo y subgrupo
        $user = Auth::user();

        $documentTypesFromGroup = collect();
        $documentTypesFromSubgroup = collect();

        // Validar si el usuario tiene un grupo asignado
        if ($user && $user->group_id) {
            if (!$user->subgroup_id) {
                $documentTypesFromGroup = DocumentType::with(['campoTypes'])->whereHas('groups', function ($q) use ($user) {
                    $q->where('groups.id', $user->group_id);
                })->whereNot('name', 'Bloque')->get();
            }
        }

        // Validar si el usuario tiene un subgrupo asignado
        if ($user && $user->subgroup_id) {
            $documentTypesFromSubgroup = DocumentType::with(['campoTypes'])->whereHas('subgroups', function ($q) use ($user) {
                $q->where('subgroups.id', $user->subgroup_id);
            })->whereNot('name', 'Bloque')->get();
        }


        // Unir ambos conjuntos y eliminar duplicados
        $userDocumentTypes = $documentTypesFromGroup
            ->merge($documentTypesFromSubgroup)
            ->unique('id')
            ->values();

        return $userDocumentTypes;
    }

    //-----------------------------------------------------------------------------
    //PRIVADOS
    private function userDocumentTypes()
    {
        // Verifica que el usuario tenga grupo y subgrupo
        $user = Auth::user();

        $documentTypesFromGroup = collect();
        $documentTypesFromSubgroup = collect();

        // Validar si el usuario tiene un grupo asignado
        if ($user && $user->group_id) {
            if (!$user->subgroup_id) {
                $documentTypesFromGroup = DocumentType::whereHas('groups', function ($q) use ($user) {
                    $q->where('groups.id', $user->group_id);
                })->whereNot('name', 'Bloque')->get();
            }
        }

        // Validar si el usuario tiene un subgrupo asignado
        if ($user && $user->subgroup_id) {
            $documentTypesFromSubgroup = DocumentType::whereHas('subgroups', function ($q) use ($user) {
                $q->where('subgroups.id', $user->subgroup_id);
            })->whereNot('name', 'Bloque')->get();
        }


        // Unir ambos conjuntos y eliminar duplicados
        $userDocumentTypes = $documentTypesFromGroup
            ->merge($documentTypesFromSubgroup)
            ->unique('id')
            ->values();

        return $userDocumentTypes;
    }

    private function storeDocumentFile($file, string $asunto): string
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($asunto) . '_' . time() . '.' . $extension;
        $area = Auth::user()->group->areaGroupType->area->descripcion;
        $folderPath = "documents/{$area}";

        return $file->storeAs($folderPath, $fileName, 'public');
    }

    private function storeDocumentFields(Document $document, array $fieldsData)
    {
        foreach ($fieldsData as $campo) {
            if (!isset($campo['id'])) {
                continue;
            }

            Campo::create([
                'document_id' => $document->id,
                'campo_type_id' => $campo['id'],
                'dato' => $campo['dato'] ?? null,
            ]);
        }
    }

    private function updateDocumentFields(Document $document, array $fieldsData)
    {
        // Eliminar campos anteriores
        $document->campos()->delete();

        // Crear nuevos campos
        foreach ($fieldsData as $campo) {
            if (!isset($campo['id'])) {
                continue;
            }

            Campo::create([
                'document_id' => $document->id,
                'campo_type_id' => $campo['id'],
                'dato' => $campo['dato'] ?? null,
            ]);
        }
    }
}
