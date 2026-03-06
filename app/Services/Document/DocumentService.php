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
    public function getShowData(Document $document): array
    {
        $document->load([
            'documentType.campoTypes',
            'documentType.groups.areaGroupType.area',
            'documentType.subgroups',
            'user:id,name,last_name',
            'campos.campoType',
        ]);

        $documentTypes = $this->userDocumentTypesWithCampos();

        return compact('document', 'documentTypes');
    }

    public function getAll($data)
    {
        $user = Auth::user();
        $query = Document::query()
            ->select([
                'id', 'n_documento', 'asunto', 'folios', 'root', 'fecha', 'periodo', 
                'user_id', 'document_type_id', 'group_id', 'subgroup_id', 'created_at'
            ])
            ->when(!$user->hasRole('ADMINISTRADOR'), function ($q) use ($user) {
                if ($user->can('documents.view.all')) {
                    return $q;
                }

                if ($user->can('documents.view.group')) {
                    if ($user->subgroup_id) {
                        return $q->where('subgroup_id', $user->subgroup_id);
                    }
                    return $q->where('group_id', $user->group_id);
                }

                if ($user->can('documents.view.own')) {
                    return $q->where('user_id', $user->id);
                }

                // Si no tiene ninguno de los 3, no ve nada (por seguridad)
                return $q->whereRaw('1 = 0');
            })
            ->with([
                'documentType:id,name',
                'documentType.campoTypes:id,name',
                'documentType.groups.areaGroupType.area:id,descripcion',
                'documentType.subgroups:id,descripcion',
                'user:id,name,last_name',
                'campos:id,document_id,campo_type_id,dato',
                'campos.campoType:id,name,data_type',
            ])
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

        $driver = DB::connection()->getDriverName();
        $yearExpression = $driver === 'pgsql'
            ? 'EXTRACT(YEAR FROM fecha)::int as year'
            : 'YEAR(fecha) as year';

        $years = \Illuminate\Support\Facades\Cache::remember('documents_available_years', now()->addHours(24), function () use ($yearExpression) {
            return Document::selectRaw($yearExpression)
                ->whereNotNull('fecha')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
        });

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
            $filePath = $file ? $this->storeDocumentFile($file, $data['asunto']) : null;

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

            \Illuminate\Support\Facades\Cache::forget('documents_available_years');

            return $document;
        });
    }

    public function update(array $data, $model, $file, $hasFile, $inputs)
    {
        return DB::transaction(function () use ($data, $model, $file, $hasFile, $inputs) {
            // Bloqueo pesimista para el documento
            $document = Document::lockForUpdate()->findOrFail($model->id);

            if ($hasFile) {
                if ($document->root) {
                    \App\Jobs\DeleteFileJob::dispatch($document->root);
                }
                $data['root'] = $this->storeDocumentFile($file, $data['asunto']);
            }

            $document->update([
                'n_documento' => $data['n_documento'] ?? $document->n_documento,
                'asunto' => $data['asunto'] ?? $document->asunto,
                'folios' => $data['folios'] ?? $document->folios,
                'fecha' => $data['fecha'] ?? $document->fecha,
                'root' => $data['root'] ?? $document->root,
                'periodo' => Carbon::parse($data['fecha'])->year ?? $document->periodo,
                'group_id' => Auth::user()->group_id,
                'subgroup_id' => Auth::user()->subgroup_id,
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
            if ($filePath) {
                \App\Jobs\DeleteFileJob::dispatch($filePath);
            }
        });
    }

    public function uploadFile(Document $model, $file): Document
    {
        return DB::transaction(function () use ($model, $file) {
            $document = Document::lockForUpdate()->findOrFail($model->id);

            if ($document->root) {
                \App\Jobs\DeleteFileJob::dispatch($document->root);
            }

            $document->update([
                'root' => $this->storeDocumentFile($file, $document->asunto),
            ]);

            return $document;
        });
    }

    //SERVICIOS PERSONALIZADOS

    //PÃšBLICOS

    public function report($data)
    {
        return Document::query()->with(['group.areaGroupType.area', 'subgroup', 'user'])
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
                $data->role_id,
                fn($q, $roleId) => $q->whereHas('user.roles', function ($q) use ($roleId) {
                    $q->where('roles.id', $roleId);
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

    public function userDocumentTypesWithCampos($user = null)
    {
        return $this->userDocumentTypes($user, true);
    }

    //-----------------------------------------------------------------------------
    //PRIVADOS
    private function userDocumentTypes($user = null, $withCamposOnly = false)
    {
        $user = $user ?: Auth::user();
        if (!$user)
            return collect();

        $cacheKey = "user_doc_types_{$user->id}_" . ($withCamposOnly ? 'campos' : 'full');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(30), function () use ($user, $withCamposOnly) {
            if ($user->hasRole('ADMINISTRADOR')) {
                $query = DocumentType::query();
                return $withCamposOnly
                    ? $query->with(['campoTypes'])->get()
                    : $query->with(['campoTypes', 'groups.areaGroupType', 'subgroups.group.areaGroupType'])->get();
            }

            $relations = $withCamposOnly
                ? ['campoTypes']
                : ['campoTypes', 'groups.areaGroupType', 'subgroups.group.areaGroupType'];

            $documentTypesFromGroup = collect();
            $documentTypesFromSubgroup = collect();

            if ($user->group_id && !$user->subgroup_id) {
                $documentTypesFromGroup = DocumentType::with($relations)
                    ->whereHas('groups', fn($q) => $q->where('groups.id', $user->group_id))
                    ->get();
            }

            if ($user->subgroup_id) {
                $documentTypesFromSubgroup = DocumentType::with($relations)
                    ->whereHas('subgroups', fn($q) => $q->where('subgroups.id', $user->subgroup_id))
                    ->get();
            }

            return $documentTypesFromGroup->merge($documentTypesFromSubgroup)->unique('id')->values();
        });
    }

    private function storeDocumentFile($file, string $asunto): string
    {
        $extension = $file->extension() ?: $file->getClientOriginalExtension();
        
        $safeAsunto = Str::limit(Str::slug($asunto), 100, '');
        $fileName = $safeAsunto . '_' . now()->getTimestampMs() . '_' . Str::random(5) . '.' . $extension;
        
        $area = Auth::user()->group?->areaGroupType?->area?->descripcion ?? 'general';
        $folderPath = "documents/" . Str::slug($area);

        return $file->storeAs($folderPath, $fileName, 'public');
    }

    private function storeDocumentFields(Document $document, array $fieldsData)
    {
        $now = now();
        $dataToInsert = collect($fieldsData)
            ->filter(fn($campo) => isset($campo['id']))
            ->map(fn($campo) => [
                'document_id' => $document->id,
                'campo_type_id' => $campo['id'],
                'dato' => $campo['dato'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

        if (!empty($dataToInsert)) {
            Campo::insert($dataToInsert);
        }
    }

    private function updateDocumentFields(Document $document, array $fieldsData)
    {
        // Eliminar campos anteriores
        $document->campos()->delete();
        $this->storeDocumentFields($document, $fieldsData);
    }
}
