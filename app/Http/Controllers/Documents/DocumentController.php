<?php

namespace App\Http\Controllers\Documents;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Http\Requests\Document\CreateDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Models\{Area, Document, DocumentType};
use App\Services\Document\DocumentService;
use Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Log;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $service)
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('user');
            return $middleware->handle($request, $next);
        });
    }

    public function index(Request $request)
    {
        $resources = $this->service->getAll($request);

        //Caché de la vista
        Cache::put('areas_list', $resources['areas'], now()->addDay());
        Cache::put('groups_list', $resources['groups'], now()->addDay());
        Cache::put('subgroups_list', $resources['subgroups'], now()->addDay());
        Cache::put("document_types_list" . Auth::id(), $resources['documentTypes'], now()->addDay());

        return view('documents.index', [
            'documents' => $resources['documents']->paginate(10),
            'areas' => $resources['areas'],
            'groups' => $resources['groups'],
            'subgroups' => $resources['subgroups'],
            'documentTypes' => $resources['documentTypes'],
            'years' => $resources['years'],
        ]);
    }

    public function create()
    {
        $documentTypes = $this->service->userDocumentTypesWithCampos(Auth::user());

        Cache::put("document_types_user_" . Auth::id(), $documentTypes, now()->addDay());

        return view('documents.create', [
            'documentTypes' => $documentTypes,
            'areas' => Area::all()
        ]);
    }

    public function store(CreateDocumentRequest $request)
    {
        try {
            $this->service->create($request->validated(), $request->file('root'), $request->input('campos', []));
            $this->clearDocumentCache();
            return redirect()->route('documents.index')->with('success', 'Documento subido exitosamente');
        } catch (\Throwable $e) {
            Log::error('Error al registrar documento: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withErrors('Ocurrió un error al registrar el documento. Intenta nuevamente.')
                ->withInput();
        }
    }

    public function show(Document $document)
    {
        Carbon::setLocale('es');
        $fecha = $document->fecha;

        // Cargar los campos adicionales con sus tipos y valor
        $campos = $document->campos()->with('campoType')->get()->map(function ($campo) {
            return [
                'nombre' => $campo->campoType->name ?? 'Sin tipo',
                'valor' => $campo->dato ?? 'Sin valor',
            ];
        });

        return view('documents.show', [
            'document' => $document->load(['documentType.campoTypes']),
            'year' => $fecha->year,
            'month' => $fecha->translatedFormat('F'),
            'day' => $fecha->day,
            'daySem' => $fecha->translatedFormat('l'),
            'campos' => $campos,  // Pasamos los campos al frontend
        ]);
    }


    public function edit(Document $document)
    {
        return view('documents.edit', [
            'document' => $document,
            'documentTypes' => DocumentType::with('campoTypes')->get()
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        try {
            $document = $this->service->update($request->validated(), $document, $request->file('root'), $request->hasFile('root'), $request->campos);

            $this->clearDocumentCache();

            return redirect()->route('documents.index')->with('success', 'Documento actualizado correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error al editar el documento: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withErrors('Ocurrió un error al editar el documento. Intenta nuevamente.')
                ->withInput();
        }
    }

    public function destroy(Document $document)
    {
        try {
            $this->service->delete($document);
            $this->clearDocumentCache();
            return redirect()->route('documents.index')->with('success', 'Documento eliminado exitosamente');
        } catch (\Throwable $e) {
            Log::error('Error al editar el bloque: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withErrors('Ocurrió un error al eliminar el documento. Intenta nuevamente.')
                ->withInput();
        }
    }

    public function generatePDFReport(Request $request)
    {
        $documents = $this->service->report($request)->get();

        return Pdf::loadView('documents.report', compact('documents'))
            ->setPaper('a4', 'landscape')
            ->stream('reporte_documentos.pdf');
    }

    protected function clearDocumentCache(Document $document = null)
    {
        $keys = [
            'admin_document_count',
            'document_types_list',
            'areas_list'
        ];

        if ($document) {
            $keys[] = 'document_' . $document->id;
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
