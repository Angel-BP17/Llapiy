<?php

namespace App\Http\Controllers\DocumentTypes;

use App\Http\Middleware\AuthMiddlewareFactory;
use App\Http\Requests\DocumentType\CreateDocumentTypeRequest;
use App\Http\Requests\DocumentType\UpdateDocumentTypeRequest;
use App\Models\Area;
use App\Models\CampoDocumentType;
use App\Models\CampoType;
use App\Models\DocumentType;
use App\Models\Group;
use App\Models\Subgroup;
use Cache;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class DocumentTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $middleware = AuthMiddlewareFactory::make('admin');
            return $middleware->handle($request, $next);
        })->except(['searchCampoTypes', 'getCampos']);
    }

    public function index(Request $request)
    {
        $name = $request->input('name');
        $areaId = $request->input('area_id');
        $groupId = $request->input('group_id');
        $subgroupId = $request->input('subgroup_id');
        $blockDocumentTypeId = DocumentType::where('name', 'Bloque')->first()->id;

        // Consulta base para DocumentType
        $query = DocumentType::query();

        // Filtrar por nombre
        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        // Filtrar por área, asumiendo que DocumentType tiene relación con Group y Group tiene relación con Area
        if ($areaId) {
            $query->whereHas('groups', function ($query) use ($areaId) {
                $query->whereHas('areaGroupType', function ($query) use ($areaId) {
                    $query->where('area_id', $areaId);
                });
            });
        }

        // Filtrar por grupo
        if ($groupId) {
            $query->whereHas('groups', function ($query) use ($groupId) {
                $query->where('groups.id', $groupId);
            });
        }

        // Filtrar por subgrupo
        if ($subgroupId) {
            $query->whereHas('subgroups', function ($query) use ($subgroupId) {
                $query->where('subgroups.id', $subgroupId);
            });
        }

        $query->when($blockDocumentTypeId, fn($q) => $q->whereNot('id', $blockDocumentTypeId));

        $documentTypes = $query->paginate(10);

        $areas = Area::with('areaGroupTypes.groups.subgroups')->get();
        $groups = Group::all();
        $subgroups = Subgroup::all();

        Cache::put('areas_list', $areas, now()->addDay());
        Cache::put('groups_list', $groups, now()->addDay());
        Cache::put('subgroups_list', $subgroups, now()->addDay());

        return view('document_types.index', compact('documentTypes', 'areas', 'groups', 'subgroups'));
    }

    public function create()
    {
        return view('document_types.create', [
            'areas' => Area::with('areaGroupTypes.groups.subgroups')->get(),
            'campoTypes' => CampoType::all(['id', 'name']),
            'groups' => Group::all(),
            'subgroups' => Subgroup::all()
        ]);
    }

    public function store(CreateDocumentTypeRequest $request)
    {

        DB::beginTransaction();

        try {

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
                        'campo_type_id' => $campoTypeId
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('document_types.index')->with('success', 'Tipo de documento creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Opcional: Log del error
            Log::error('Error al crear tipo de documento: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors('Hubo un error al crear el tipo de documento.');
        }
    }

    public function edit(DocumentType $documentType)
    {
        return view('document_types.edit', [
            'documentType' => $documentType->load(['groups', 'subgroups', 'campoTypes']),
            'campoTypes' => CampoType::all(['id', 'name']),
            'areas' => Area::with('areaGroupTypes.groups.subgroups.subgroups')->get(),
        ]);
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType)
    {

        DB::beginTransaction();

        try {
            $documentType->update(['name' => $request->name]);

            // Campos
            $campoTypeIds = json_decode($request->campos, true);
            $campoTypeIds = is_array($campoTypeIds) ? $campoTypeIds : [];
            $validCampoIds = CampoType::whereIn('id', $campoTypeIds)->pluck('id')->all();
            $documentType->campoTypes()->sync($validCampoIds);

            // Grupos
            $groupIds = json_decode($request->groups, true);
            $groupIds = is_array($groupIds) ? $groupIds : [];
            $validGroupIds = Group::whereIn('id', $groupIds)->pluck('id')->all();
            $documentType->groups()->sync($validGroupIds);

            // Subgrupos
            $subgroupIds = json_decode($request->subgroups, true);
            $subgroupIds = is_array($subgroupIds) ? $subgroupIds : [];
            $validSubgroupIds = Subgroup::whereIn('id', $subgroupIds)->pluck('id')->all();
            $documentType->subgroups()->sync($validSubgroupIds);

            DB::commit();

            return redirect()->route('document_types.index')
                ->with('success', 'Tipo de documento actualizado correctamente.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al actualizar tipo de documento: ' . $e->getMessage());
            return back()->withErrors('Ocurrió un error al actualizar el tipo de documento.')->withInput();
        }
    }

    public function destroy(DocumentType $documentType)
    {
        CampoDocumentType::where('document_type_id', $documentType->id)->delete();
        $documentType->delete();

        return redirect()->route('document_types.index')->with('success', 'Tipo de documento eliminado correctamente.');
    }
}
