<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\IndexDocumentRequest;
use App\Http\Requests\Document\CreateDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Http\Requests\Document\UploadDocumentFileRequest;
use App\Models\Document;
use App\Services\Document\DocumentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected DocumentService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexDocumentRequest $request): Response
    {
        $resources = $this->service->getAll($request);
        $query = $resources['documents'];

        $totalDocuments = (clone $query)->count();
        $attendedCount = (clone $query)
            ->whereNotNull('root')
            ->where('root', '!=', '')
            ->count();
        $unattendedCount = max($totalDocuments - $attendedCount, 0);

        $paginatedDocuments = $query->paginate(10);
        
        $paginatedDocuments->getCollection()->transform(function ($doc) {
            $doc->load([
                'documentType', 
                'user', 
                'campos.campoType', 
                'group.areaGroupType.area', 
                'subgroup'
            ]);
            $doc->can = [
                'update' => auth()->user()->can('update', $doc),
                'delete' => auth()->user()->can('delete', $doc),
                'view' => auth()->user()->can('view', $doc),
            ];
            return $doc;
        });

        return Inertia::render('documents/index', [
            'documents' => $paginatedDocuments->items(),
            'documentTypes' => $resources['documentTypes'],
            'areas' => $resources['areas'],
            'groups' => $resources['groups'],
            'subgroups' => $resources['subgroups'],
            'years' => $resources['years'],
            'stats' => [
                'totalDocuments' => $totalDocuments,
                'attendedCount' => $attendedCount,
                'unattendedCount' => $unattendedCount,
            ],
            'filters' => $request->only(['asunto', 'document_type_id', 'area_id', 'group_id', 'subgroup_id', 'year', 'month']),
            'pagination' => [
                'total' => $paginatedDocuments->total(),
                'current_page' => $paginatedDocuments->currentPage(),
                'last_page' => $paginatedDocuments->lastPage(),
                'from' => $paginatedDocuments->firstItem(),
                'to' => $paginatedDocuments->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDocumentRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request->validated(), $request->file('root'), $request->input('campos', []));
            return redirect()->back()->with('message', 'Documento creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrio un error al registrar el documento.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document): Response
    {
        $this->authorize('view', $document);
        $document->load([
            'documentType',
            'user',
            'group.areaGroupType.area',
            'campos.campoType'
        ]);

        return Inertia::render('documents/show', [
            'document' => $document,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        try {
            $this->authorize('update', $document);
            $this->service->update(
                $request->validated(),
                $document,
                $request->file('root'),
                $request->hasFile('root'),
                $request->campos
            );

            return redirect()->back()->with('message', 'Documento actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al editar el documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrio un error al editar el documento.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document): RedirectResponse
    {
        try {
            $this->authorize('delete', $document);
            $this->service->delete($document);

            return redirect()->back()->with('message', 'Documento eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar el documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrio un error al eliminar el documento.');
        }
    }

    /**
     * View/Download the document file.
     */
    public function file(Document $document)
    {
        $this->authorize('view', $document);
        if (!$document->root || !Storage::disk('public')->exists($document->root)) {
            return response()->json(['message' => 'El archivo no existe o no ha sido cargado.'], 404);
        }

        return Storage::disk('public')->response($document->root);
    }

    /**
     * Upload or update the document file.
     */
    public function upload(UploadDocumentFileRequest $request, Document $document): RedirectResponse
    {
        try {
            $this->service->uploadFile($document, $request->file('root'));
            return redirect()->back()->with('message', 'Archivo del documento actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al subir archivo del documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrio un error al subir el archivo del documento.');
        }
    }

    /**
     * Generate PDF report.
     */
    public function pdf(Request $request)
    {
        try {
            $documents = $this->service->report($request)->get();

            return Pdf::loadView('documents.report', compact('documents'))
                ->setPaper('a4', 'landscape')
                ->stream('reporte_documentos.pdf');
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo generar el reporte de documentos.'], 500);
        }
    }
}
