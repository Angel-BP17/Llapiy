<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\CreateDocumentRequest;
use App\Http\Requests\Document\IndexDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Http\Requests\Document\UploadDocumentFileRequest;
use App\Models\Document;
use App\Models\DocumentType;
use App\Services\Document\DocumentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $service)
    {
    }

    public function index(IndexDocumentRequest $request)
    {
        $resources = $this->service->getAll($request);
        $documentTypes = $resources['documentTypes'];
        $selectedDocumentTypeScope = (string) $request->query('document_type_scope', '');

        [$documentTypesCount, $documentTypesCountLabel] = $this->resolveDocumentTypesCounter(
            $documentTypes,
            $resources,
            $selectedDocumentTypeScope
        );

        Cache::put('areas_list', $resources['areas'], now()->addDay());
        Cache::put('groups_list', $resources['groups'], now()->addDay());
        Cache::put('subgroups_list', $resources['subgroups'], now()->addDay());
        Cache::put('document_types_list' . Auth::id(), $documentTypes, now()->addDay());

        return $this->apiSuccess('Documentos obtenidos correctamente.', [
            'documents' => $resources['documents']->paginate(10),
            'areas' => $resources['areas'],
            'groups' => $resources['groups'],
            'subgroups' => $resources['subgroups'],
            'documentTypes' => $documentTypes,
            'documentTypesCount' => $documentTypesCount,
            'documentTypesCountLabel' => $documentTypesCountLabel,
            'selectedDocumentTypeScope' => $selectedDocumentTypeScope,
            'years' => $resources['years'],
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'createDocumentTypes' => $this->service->userDocumentTypesWithCampos(Auth::user()),
            'allDocumentTypes' => DocumentType::with('campoTypes')->get(),
            'totalDocuments' => Document::count(),
        ]);
    }

    public function store(CreateDocumentRequest $request)
    {
        try {
            $document = $this->service->create($request->validated(), $request->file('root'), $request->input('campos', []));
            $this->clearDocumentCache($document);

            return $this->apiSuccess('Documento creado correctamente.', ['document' => $document], 201);
        } catch (\Throwable $e) {
            Log::error('Error al registrar documento: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->apiError('Ocurrio un error al registrar el documento. Intenta nuevamente.', 500);
        }
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        try {
            $updated = $this->service->update(
                $request->validated(),
                $document,
                $request->file('root'),
                $request->hasFile('root'),
                $request->campos
            );

            $this->clearDocumentCache($document);

            return $this->apiSuccess('Documento actualizado correctamente.', ['document' => $updated]);
        } catch (\Throwable $e) {
            Log::error('Error al editar el documento: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->apiError('Ocurrio un error al editar el documento. Intenta nuevamente.', 500);
        }
    }

    public function uploadFile(UploadDocumentFileRequest $request, Document $document)
    {
        try {
            $updated = $this->service->uploadFile($document, $request->file('root'));
            $this->clearDocumentCache($document);

            return $this->apiSuccess('Archivo del documento actualizado correctamente.', ['document' => $updated]);
        } catch (\Throwable $e) {
            Log::error('Error al subir archivo del documento: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->apiError('Ocurrio un error al subir el archivo del documento. Intenta nuevamente.', 500);
        }
    }

    public function destroy(Document $document)
    {
        try {
            $this->service->delete($document);
            $this->clearDocumentCache($document);

            return $this->apiSuccess('Documento eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar el documento: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->apiError('Ocurrio un error al eliminar el documento. Intenta nuevamente.', 500);
        }
    }

    public function generatePDFReport(Request $request)
    {
        $documents = $this->service->report($request)->get();

        return Pdf::loadView('documents.report', compact('documents'))
            ->setPaper('a4', 'landscape')
            ->stream('reporte_documentos.pdf');
    }

    protected function clearDocumentCache(?Document $document = null): void
    {
        $keys = [
            'admin_document_count',
            'document_types_list',
            'document_types_list' . Auth::id(),
            'areas_list',
            'groups_list',
            'subgroups_list',
        ];

        if ($document) {
            $keys[] = 'document_' . $document->id;
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    protected function resolveDocumentTypesCounter(
        Collection $documentTypes,
        array $resources,
        string $selectedScope
    ): array {
        $defaultLabel = 'Tipos de documento disponibles';

        if (!(Auth::user()?->hasRole('ADMINISTRADOR')) || $selectedScope === '') {
            return [$documentTypes->count(), $defaultLabel];
        }

        [$scopeType, $scopeId] = array_pad(explode(':', $selectedScope, 2), 2, null);
        $scopeId = (int) $scopeId;

        if ($scopeId <= 0) {
            return [$documentTypes->count(), $defaultLabel];
        }

        if ($scopeType === 'area') {
            $filtered = $documentTypes->filter(function ($documentType) use ($scopeId) {
                $byGroup = $documentType->groups->contains(
                    fn($group) => (int) ($group->areaGroupType?->area_id ?? 0) === $scopeId
                );
                $bySubgroup = $documentType->subgroups->contains(
                    fn($subgroup) => (int) ($subgroup->group?->areaGroupType?->area_id ?? 0) === $scopeId
                );

                return $byGroup || $bySubgroup;
            });
            $labelTarget = $resources['areas']->firstWhere('id', $scopeId)?->descripcion;

            return [$filtered->unique('id')->count(), $labelTarget ? "Tipos de documento del area: {$labelTarget}" : $defaultLabel];
        }

        if ($scopeType === 'group') {
            $filtered = $documentTypes->filter(function ($documentType) use ($scopeId) {
                $byGroup = $documentType->groups->contains(fn($group) => (int) $group->id === $scopeId);
                $bySubgroup = $documentType->subgroups->contains(fn($subgroup) => (int) ($subgroup->group_id ?? 0) === $scopeId);

                return $byGroup || $bySubgroup;
            });
            $labelTarget = $resources['groups']->firstWhere('id', $scopeId)?->descripcion;

            return [$filtered->unique('id')->count(), $labelTarget ? "Tipos de documento del grupo: {$labelTarget}" : $defaultLabel];
        }

        if ($scopeType === 'subgroup') {
            $filtered = $documentTypes->filter(
                fn($documentType) => $documentType->subgroups->contains(fn($subgroup) => (int) $subgroup->id === $scopeId)
            );
            $labelTarget = $resources['subgroups']->firstWhere('id', $scopeId)?->descripcion;

            return [$filtered->unique('id')->count(), $labelTarget ? "Tipos de documento del subgrupo: {$labelTarget}" : $defaultLabel];
        }

        return [$documentTypes->count(), $defaultLabel];
    }
}
