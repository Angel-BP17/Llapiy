@extends('layouts.app')

@section('title', 'Tipos de documentos')
@section('content')
    @vite('resources/js/document_types/index.js')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Tipos de documentos</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Tipos de documentos</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms module-ui">
        <div class="container-fluid">
            <!-- Mostrar mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="card module-hero has-shadow mb-4">
                <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <div class="module-hero-chip mb-2">
                            <i class="fa-solid fa-file-invoice"></i> Configuración documental
                        </div>
                        <h4 class="mb-1 text-white">Tipos de documentos</h4>
                        <p class="mb-0 module-hero-text text-white-50">Estructura tipos, campos y clasificación por áreas,
                            grupos y subgrupos.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $documentTypes->total() }} registros
                        </span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-blue"><i class="fa-solid fa-file-invoice"></i></div>
                            <div>
                                <div class="text-muted small">Tipos de documentos</div>
                                <div class="h4 mb-0">{{ $totalDocumentTypes }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-violet"><i class="fa-solid fa-list-check"></i></div>
                            <div>
                                <div class="text-muted small">Campos disponibles</div>
                                <div class="h4 mb-0">{{ $totalCampos }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <button class="btn btn-success" type="button" data-toggle="modal"
                        data-target="#documentTypeCreateModal">
                        <i class="fa-solid fa-file-invoice"></i> Crear Nuevo Tipo de Documento
                    </button>
                </div>
            </div>
            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body">
                    <h4>Buscar</h4>
                    <form method="GET" action="{{ route('document_types.index') }}" class="mb-0">
                        <div class="row mt-3 align-items-end">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="name" class="sr-only">Nombre</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ request('search') }}" placeholder="Ingrese el nombre">
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="area_id" class="sr-only">Área</label>
                                <select name="area_id" id="area_id" class="form-control">
                                    <option value="">-- Seleccionar Área --</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="group_id" class="sr-only">Grupo</label>
                                <select name="group_id" id="group_id" class="form-control">
                                    <option value="">-- Seleccionar Grupo --</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}"
                                            {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                            {{ $group->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="subgroup_id" class="sr-only">Subgrupo</label>
                                <select name="subgroup_id" id="subgroup_id" class="form-control">
                                    <option value="">-- Seleccionar Subgrupo</option>
                                    @foreach ($subgroups as $subgroup)
                                        {{ $subgroup->descripcion }}
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-auto mb-3">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i>
                                    Aplicar
                                    Filtros</button>
                            </div>
                            <div class="col-12 col-md-auto mb-3">
                                <a href="{{ route('document_types.index') }}" class="btn btn-secondary"><i
                                        class="fa-solid fa-x"></i> Limpiar Filtros</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card module-table-card bg-white has-shadow mb-4">
                <div class="table-responsive">
                    <table class="table module-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Campos</th>
                                <th>Grupos</th>
                                <th>Subgrupos</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documentTypes as $index => $documentType)
                                <tr>
                                    <td>{{ $documentTypes->firstItem() + $index }}</td>
                                    <td>{{ $documentType->name }}</td>
                                    <td>
                                        {{-- Botón para mostrar campos en un modal --}}
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#modalCampos{{ $documentType->id }}">
                                            <i class="fa-solid fa-eye mr-1"></i>Ver
                                        </button>

                                        {{-- Modal de campos --}}
                                        <div class="modal fade" id="modalCampos{{ $documentType->id }}" tabindex="-1"
                                            role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Campos de "{{ $documentType->name }}"</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <ul>
                                                            @foreach ($documentType->campoTypes as $campoType)
                                                                <li>{{ $campoType->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#modalGrupos{{ $documentType->id }}">
                                            <i class="fa-solid fa-eye mr-1"></i>Ver
                                        </button>

                                        <div class="modal fade" id="modalGrupos{{ $documentType->id }}" tabindex="-1"
                                            role="dailog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-tittle">Grupos de "{{ $documentType->name }}"
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <ul>
                                                            @if (!$documentType->groups->isEmpty())
                                                                @foreach ($documentType->groups as $group)
                                                                    <li>{{ $group->descripcion }}</li>
                                                                @endforeach
                                                            @else
                                                                <p>Este tipo de documento no está relacionado a ningún
                                                                    grupo</p>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#modalSubgrupos{{ $documentType->id }}">
                                            <i class="fa-solid fa-eye mr-1"></i>Ver
                                        </button>

                                        <div class="modal fade" id="modalSubgrupos{{ $documentType->id }}"
                                            tabindex="-1" role="dailog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-tittle">Subgrupos de "{{ $documentType->name }}"
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <ul>
                                                            @if (!$documentType->subgroups->isEmpty())
                                                                @foreach ($documentType->subgroups as $subgroup)
                                                                    <li>{{ $subgroup->descripcion }}</li>
                                                                @endforeach
                                                            @else
                                                                <p>Este tipo de documento no está relacionado a ningún
                                                                    subgrupo</p>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $documentTypeData = [
                                                'id' => $documentType->id,
                                                'name' => $documentType->name,
                                                'campoTypes' => $documentType->campoTypes->pluck('id')->values(),
                                                'groups' => $documentType->groups->pluck('id')->values(),
                                                'subgroups' => $documentType->subgroups->pluck('id')->values(),
                                            ];
                                        @endphp
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning btn-sm js-document-type-edit"
                                                data-toggle="modal" data-target="#documentTypeEditModal"
                                                data-document-type='@json($documentTypeData)'>
                                                <i class="fa-solid fa-pen"></i> Editar
                                            </button>
                                            <form action="{{ route('document_types.destroy', $documentType) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar este tipo de documento? Se borrará permanentemente')"
                                                    @if ($documentType->documents_count > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene documentos asociados" @endif>
                                                    <i class="fa-solid fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron tipos de documentos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $documentTypes->links() }}
            </div>
        </div>
    </section>
    <!-- Modales -->
    <div class="modal fade" id="documentTypeCreateModal" tabindex="-1" role="dialog"
        aria-labelledby="documentTypeCreateLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentTypeCreateLabel">Crear tipo de documento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('document_types.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_modal" value="create">
                    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                        @if ($errors->any() && old('_modal') === 'create')
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="dt_create_name">Nombre del Tipo de Documento</label>
                            <input type="text" class="form-control" id="dt_create_name" name="name"
                                value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="d-flex align-items-center justify-content-between">
                                <span>Seleccionar Áreas, Grupos y Subgrupos</span>
                                <small class="text-muted">Marca una área para seleccionar todo su árbol.</small>
                            </label>
                            <div class="dt-selector-panel">
                                <div class="row">
                                    <div class="col-12 col-lg-7 mb-3 mb-lg-0">
                                        <div class="d-flex align-items-center justify-content-end mb-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="dt_create_expandAllBtn">Expandir todo</button>
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="dt_create_collapseAllBtn">Contraer todo</button>
                                            </div>
                                        </div>
                                        <div class="input-group input-group-sm mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="fa-solid fa-magnifying-glass"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="dt_create_treeSearch"
                                                placeholder="Buscar área, grupo o subgrupo...">
                                        </div>
                                        <div id="dt_create_groupTree" class="dt-group-tree border rounded p-2"></div>
                                    </div>
                                    <div class="col-12 col-lg-5">
                                        <div class="dt-selected-panel border rounded p-2">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <h6 class="mb-0">Seleccionados</h6>
                                                <div>
                                                    <span id="dt_create_selectedCounter"
                                                        class="badge badge-pill badge-primary mr-1">0</span>
                                                    <button type="button" class="btn btn-link btn-sm p-0"
                                                        id="dt_create_clearSelectionBtn">Limpiar</button>
                                                </div>
                                            </div>
                                            <div id="dt_create_selectedItemsList" class="dt-selected-items"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="groups" id="dt_create_selectedGroupsInput">
                        <input type="hidden" name="subgroups" id="dt_create_selectedSubgroupsInput">
                        <div class="form-group">
                            <label class="d-flex align-items-center justify-content-between">
                                <span>Seleccionar Campos</span>
                                <small class="text-muted">Busca y marca los campos necesarios.</small>
                            </label>
                            <div class="dt-campo-panel">
                                <div class="row">
                                    <div class="col-12 col-lg-7 mb-3 mb-lg-0">
                                        <div class="input-group input-group-sm mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="fa-solid fa-magnifying-glass"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="dt_create_campoSearch"
                                                placeholder="Buscar campo por nombre...">
                                        </div>
                                        <div id="dt_create_campoResults" class="dt-campo-results"></div>
                                    </div>
                                    <div class="col-12 col-lg-5">
                                        <div class="dt-selected-panel border rounded p-2">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <h6 class="mb-0">Campos seleccionados</h6>
                                                <div>
                                                    <span id="dt_create_camposCounter"
                                                        class="badge badge-pill badge-success mr-1">0</span>
                                                    <button type="button" class="btn btn-link btn-sm p-0"
                                                        id="dt_create_clearCamposBtn">Limpiar</button>
                                                </div>
                                            </div>
                                            <div id="dt_create_camposSeleccionados" class="dt-selected-items"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="campos" id="dt_create_camposInput">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="documentTypeEditModal" tabindex="-1" role="dialog"
        aria-labelledby="documentTypeEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentTypeEditLabel">Editar tipo de documento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="documentTypeEditForm"
                    action="{{ route('document_types.update', ['document_type' => 'DT_ID']) }}"
                    data-action-template="{{ route('document_types.update', ['document_type' => 'DT_ID']) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="edit">
                    <input type="hidden" name="edit_document_type_id" id="edit_document_type_id"
                        value="{{ old('edit_document_type_id') }}">
                    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                        @if ($errors->any() && old('_modal') === 'edit')
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="dt_edit_name">Nombre del Tipo de Documento</label>
                            <input type="text" class="form-control" id="dt_edit_name" name="name"
                                value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="d-flex align-items-center justify-content-between">
                                <span>Seleccionar Áreas, Grupos y Subgrupos</span>
                                <small class="text-muted">Marca una área para seleccionar todo su árbol.</small>
                            </label>
                            <div class="dt-selector-panel">
                                <div class="row">
                                    <div class="col-12 col-lg-7 mb-3 mb-lg-0">
                                        <div class="d-flex align-items-center justify-content-end mb-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="dt_edit_expandAllBtn">Expandir todo</button>
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="dt_edit_collapseAllBtn">Contraer todo</button>
                                            </div>
                                        </div>
                                        <div class="input-group input-group-sm mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="fa-solid fa-magnifying-glass"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="dt_edit_treeSearch"
                                                placeholder="Buscar área, grupo o subgrupo...">
                                        </div>
                                        <div id="dt_edit_groupTree" class="dt-group-tree border rounded p-2"></div>
                                    </div>
                                    <div class="col-12 col-lg-5">
                                        <div class="dt-selected-panel border rounded p-2">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <h6 class="mb-0">Seleccionados</h6>
                                                <div>
                                                    <span id="dt_edit_selectedCounter"
                                                        class="badge badge-pill badge-primary mr-1">0</span>
                                                    <button type="button" class="btn btn-link btn-sm p-0"
                                                        id="dt_edit_clearSelectionBtn">Limpiar</button>
                                                </div>
                                            </div>
                                            <div id="dt_edit_selectedItemsList" class="dt-selected-items"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="groups" id="dt_edit_selectedGroupsInput">
                        <input type="hidden" name="subgroups" id="dt_edit_selectedSubgroupsInput">
                        <div class="form-group">
                            <label class="d-flex align-items-center justify-content-between">
                                <span>Seleccionar Campos</span>
                                <small class="text-muted">Busca y marca los campos necesarios.</small>
                            </label>
                            <div class="dt-campo-panel">
                                <div class="row">
                                    <div class="col-12 col-lg-7 mb-3 mb-lg-0">
                                        <div class="input-group input-group-sm mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="fa-solid fa-magnifying-glass"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="dt_edit_campoSearch"
                                                placeholder="Buscar campo por nombre...">
                                        </div>
                                        <div id="dt_edit_campoResults" class="dt-campo-results"></div>
                                    </div>
                                    <div class="col-12 col-lg-5">
                                        <div class="dt-selected-panel border rounded p-2">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <h6 class="mb-0">Campos seleccionados</h6>
                                                <div>
                                                    <span id="dt_edit_camposCounter"
                                                        class="badge badge-pill badge-success mr-1">0</span>
                                                    <button type="button" class="btn btn-link btn-sm p-0"
                                                        id="dt_edit_clearCamposBtn">Limpiar</button>
                                                </div>
                                            </div>
                                            <div id="dt_edit_camposSeleccionados" class="dt-selected-items"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="campos" id="dt_edit_camposInput">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="document-types-page-data" class="d-none" data-areas='@json($areas)'
        data-campo-types='@json($campoTypes)' data-old-modal='@json(old('_modal'))'
        data-old-edit-id='@json(old('edit_document_type_id'))' data-old-edit-name='@json(old('name'))'
        data-old-edit-campos='@json(old('campos', []))' data-old-edit-groups='@json(old('groups', []))'
        data-old-edit-subgroups='@json(old('subgroups', []))'></div>
    <style>
        .dt-selector-panel {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
        }

        .dt-group-tree {
            max-height: 340px;
            overflow-y: auto;
            background: #fff;
        }

        .dt-area-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            margin-bottom: 8px;
        }

        .dt-area-head {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 8px 8px 0 0;
            padding: 8px 10px;
        }

        .dt-area-body {
            padding: 8px 10px;
        }

        .dt-group-row,
        .dt-subgroup-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .dt-subgroup-children {
            margin-left: 24px;
            padding-left: 10px;
            border-left: 2px dashed #d1d5db;
        }

        .dt-selected-items {
            min-height: 58px;
            max-height: 250px;
            overflow-y: auto;
            background: #fff;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 8px;
        }

        .dt-selection-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0 6px 6px 0;
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 12px;
            border: 1px solid #dbeafe;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .dt-selection-chip-green {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .dt-campo-panel {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
        }

        .dt-campo-results {
            min-height: 120px;
            max-height: 250px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px;
        }

        .dt-campo-item {
            display: flex;
            align-items: center;
            padding: 6px 4px;
            border-bottom: 1px solid #f1f5f9;
        }

        .dt-campo-item:last-child {
            border-bottom: 0;
        }

        .dt-highlight {
            background: #fef08a;
            color: inherit;
            padding: 0 1px;
            border-radius: 2px;
        }

        .dt-selection-chip button {
            border: 0;
            background: transparent;
            color: #1e3a8a;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
            padding: 0;
        }

        .dt-empty-selection {
            color: #64748b;
            font-size: 13px;
            margin: 0;
        }
    </style>
@endsection
