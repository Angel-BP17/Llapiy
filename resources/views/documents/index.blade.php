@extends('layouts.app')

@section('title', 'Documentos')
@section('content')
    @vite('resources/js/documents/index.js')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Documentos</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Documentos</li>
        </ul>
    </div>

    <!-- Tabla de documentos -->
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
                            <i class="fa-solid fa-file-lines"></i> Flujo documental
                        </div>
                        <h4 class="mb-0 mt-2 text-white">Gestión de documentos</h4>
                        <p class="mb-0 module-hero-text text-white-50">Consulta, filtra y administra registros documentales
                            con
                            trazabilidad.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $documents->total() }} registros
                        </span>
                    </div>
                </div>
            </div>
            @php
                $isAdmin = auth()->user()?->hasRole('ADMINISTRADOR');
            @endphp
            <div class="row mb-3">
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-blue"><i class="fa-regular fa-file"></i></div>
                            <div>
                                <div class="text-muted small">Total de documentos</div>
                                <div class="h4 mb-0">{{ $totalDocuments }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon mr-3 text-violet"><i class="fa-solid fa-layer-group"></i></div>
                                <div>
                                    <div class="text-muted small">{{ $documentTypesCountLabel }}</div>
                                    <div class="h4 mb-0">{{ $documentTypesCount }}</div>
                                </div>
                            </div>
                            @if ($isAdmin)
                                <form method="GET" action="{{ route('documents.index') }}" class="mt-3">
                                    @foreach (request()->except(['document_type_scope', 'page']) as $name => $value)
                                        @if (!is_array($value) && $value !== null && $value !== '')
                                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                                        @endif
                                    @endforeach
                                    <label for="document_type_scope" class="small text-muted mb-1">Contar por
                                        ámbito</label>
                                    <select name="document_type_scope" id="document_type_scope" class="form-control"
                                        onchange="this.form.submit()">
                                        <option value="">Todos los tipos disponibles</option>
                                        <optgroup label="Áreas">
                                            @foreach ($areas as $area)
                                                <option value="area:{{ $area->id }}" @selected($selectedDocumentTypeScope === 'area:' . $area->id)>
                                                    Área: {{ $area->descripcion }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Grupos">
                                            @foreach ($groups as $group)
                                                <option value="group:{{ $group->id }}" @selected($selectedDocumentTypeScope === 'group:' . $group->id)>
                                                    Grupo: {{ $group->descripcion }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Subgrupos">
                                            @foreach ($subgroups as $subgroup)
                                                <option value="subgroup:{{ $subgroup->id }}" @selected($selectedDocumentTypeScope === 'subgroup:' . $subgroup->id)>
                                                    Subgrupo: {{ $subgroup->descripcion }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    @if ($documentTypes->count() < 1)
                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" data-placement="right"
                            title="Para ingresar un documento, primero cree un tipo de documento en el módulo de Inf. adicional de documentos">
                            <button class="ml-right btn btn-success disabled" type="button">
                                <i class="fa-regular fa-file"></i> Ingresar Documento
                            </button>
                        </span>
                    @else
                        <button class="ml-right btn btn-success" type="button" data-toggle="modal"
                            data-target="#documentCreateModal">
                            <i class="fa-regular fa-file"></i> Ingresar Documento
                        </button>
                    @endif
                </div>
            </div>
            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body py-3">
                    @php
                        $pdfUrl = route('documents.pdfReport', request()->all());
                    @endphp
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3 rounded px-3 py-2"
                        style="background: linear-gradient(90deg, rgba(52, 152, 219, 0.14), rgba(46, 204, 113, 0.10)); border: 1px solid rgba(52, 152, 219, 0.22);">
                        <div>
                            <h5 class="mb-1"><i class="fa-solid fa-sliders mr-2"></i>Filtros de búsqueda</h5>
                            <p class="text-muted small mb-0">Filtra por estructura organizacional, tipo y periodo.</p>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('documents.index') }}">
                        @if ($isAdmin && $selectedDocumentTypeScope)
                            <input type="hidden" name="document_type_scope" value="{{ $selectedDocumentTypeScope }}">
                        @endif

                        <div class="row">
                            <div class="col-12 col-lg-6 mb-2">
                                <label for="asunto" class="small text-muted mb-1">Asunto</label>
                                <input type="text" name="asunto" id="asunto" class="form-control"
                                    value="{{ request('asunto') }}" placeholder="Ej. informe, oficio, solicitud">
                            </div>
                            <div class="col-12 col-lg-6 mb-2">
                                <label for="document_type_id" class="small text-muted mb-1">Tipo de documento</label>
                                <select name="document_type_id" id="document_type_id" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach ($documentTypes as $type)
                                        @if ($type->name !== 'Bloque')
                                            <option value="{{ $type->id }}"
                                                {{ request('document_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2 align-items-center">
                            <span class="badge badge-pill badge-info px-3 py-2 ml-2 mb-2">Ubicación
                                organizacional</span>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-3 mb-2">
                                <label for="area_id" class="small text-muted mb-1">Área</label>
                                <select name="area_id" id="area_id" class="form-control">
                                    <option value="">Todas</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3 mb-2">
                                <label for="group_id" class="small text-muted mb-1">Grupo</label>
                                <select name="group_id" id="group_id" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}"
                                            {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                            {{ $group->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3 mb-2">
                                <label for="subgroup_id" class="small text-muted mb-1">Subgrupo</label>
                                <select name="subgroup_id" id="subgroup_id" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach ($subgroups as $subgroup)
                                        <option value="{{ $subgroup->id }}"
                                            {{ request('subgroup_id') == $subgroup->id ? 'selected' : '' }}>
                                            {{ $subgroup->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3 mb-2">
                                <label for="role_id" class="small text-muted mb-1">Rol de usuario</label>
                                <select name="role_id" id="role_id" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ (string) request('role_id') === (string) $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <span class="badge badge-pill badge-primary px-3 ml-2 mb-2 py-2">Periodo</span>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-2">
                                <label for="year" class="small text-muted mb-1">Año</label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}"
                                            {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <label for="month" class="small text-muted mb-1">Mes</label>
                                <select name="month" id="month" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach ([1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'] as $key => $month)
                                        <option value="{{ $key }}"
                                            {{ request('month') == $key ? 'selected' : '' }}>
                                            {{ $month }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div
                            class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center border-top pt-2 mt-2">
                            <p class="text-muted small mb-2 mb-md-0">Combina filtros para ubicar documentos con menos
                                pasos.</p>
                            <div class="d-flex flex-column flex-sm-row">
                                <button type="submit" class="btn btn-primary btn-sm mr-sm-2 mb-2 mb-sm-0">
                                    <i class="fa-solid fa-filter"></i> Aplicar filtros
                                </button>
                                <a href="{{ route('documents.index') }}"
                                    class="btn btn-outline-secondary btn-sm mr-sm-2 mb-2 mb-sm-0">
                                    <i class="fa-solid fa-rotate-left"></i> Limpiar
                                </a>
                                @if ($documents->count() < 1)
                                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                                        title="Para generar un reporte debe haber ingresado al menos 1 documento">
                                        <a href="{{ $pdfUrl }}" class="btn btn-danger btn-sm disabled"
                                            target="_blank">
                                            <i class="fa-solid fa-file-pdf"></i> Generar reporte
                                        </a>
                                    </span>
                                @else
                                    <a href="{{ $pdfUrl }}" class="btn btn-danger btn-sm" target="_blank">
                                        <i class="fa-solid fa-file-pdf"></i> Generar reporte
                                    </a>
                                @endif
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
                                <th>#</th>
                                <th>N° de documento</th>
                                <th>Asunto</th>
                                <th>Folios</th>
                                <th>Tipo de documento</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documents as $index => $document)
                                <tr>
                                    <td>{{ $documents->firstItem() + $index }}</td>
                                    <td>{{ $document->n_documento }}</td>
                                    <td>{{ $document->asunto }}</td>
                                    <td>{{ $document->folios }}</td>
                                    <td>{{ $document->documentType->name }}</td>
                                    <td class="text-end">
                                        @php
                                            $documentData = [
                                                'id' => $document->id,
                                                'n_documento' => $document->n_documento,
                                                'asunto' => $document->asunto,
                                                'folios' => $document->folios,
                                                'fecha' => optional($document->fecha)->format('Y-m-d'),
                                                'document_type_id' => $document->document_type_id,
                                                'document_type_name' => $document->documentType->name ?? null,
                                                'root_url' => $document->root
                                                    ? asset('storage/' . $document->root)
                                                    : null,
                                                'campos' => $document->campos
                                                    ->map(function ($campo) {
                                                        return [
                                                            'campo_type_id' => $campo->campo_type_id,
                                                            'name' => $campo->campoType->name ?? '',
                                                            'dato' => $campo->dato,
                                                        ];
                                                    })
                                                    ->values(),
                                            ];
                                        @endphp
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info btn-sm js-document-show"
                                                data-toggle="modal" data-target="#documentShowModal"
                                                data-document='@json($documentData)'>
                                                <i class="fa-solid fa-eye"></i> Ver
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm js-document-edit"
                                                data-toggle="modal" data-target="#documentEditModal"
                                                data-document='@json($documentData)'>
                                                <i class="fa-solid fa-pen"></i> Editar
                                            </button>
                                            <form action="{{ route('documents.destroy', $document) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar este documento? Se borrará permanentemente')">
                                                    <i class="fa-solid fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron documentos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $documents->links() }}
            </div>

        </div>
    </section>
    <!-- Modales -->
    <div class="modal fade" id="documentCreateModal" tabindex="-1" role="dialog"
        aria-labelledby="documentCreateLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentCreateLabel">Ingresar documento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
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
                        <div class="row mb-4">
                            <div class="col-12 col-lg-4">
                                <label for="document_create_type" class="form-control-label">Tipo de Documento</label>
                                <select name="document_type_id" id="document_create_type" class="form-control" required>
                                    <option value="">Seleccione un tipo</option>
                                    @foreach ($createDocumentTypes as $type)
                                        <option value="{{ $type->id }}" @selected(old('document_type_id') == $type->id)>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="p-4 bg-white has-shadow">
                            <h5 class="mb-3">Campos generales</h5>
                            <div class="row">
                                <div class="col-sm form-group">
                                    <label for="document_create_n_documento" class="form-control-label">Nº de
                                        Documento</label>
                                    <input type="text" name="n_documento" id="document_create_n_documento"
                                        class="form-control" value="{{ old('n_documento') }}" required>
                                </div>
                                <div class="col-sm form-group">
                                    <label for="document_create_asunto" class="form-control-label">Asunto</label>
                                    <input type="text" class="form-control" id="document_create_asunto"
                                        name="asunto" value="{{ old('asunto') }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm form-group">
                                    <label for="document_create_folios" class="form-control-label">Folios</label>
                                    <input type="text" class="form-control" id="document_create_folios"
                                        name="folios" value="{{ old('folios') }}">
                                </div>
                                <div class="col-sm form-group">
                                    <label for="document_create_fecha" class="form-control-label">Fecha del
                                        documento</label>
                                    <input type="date" class="form-control" id="document_create_fecha" name="fecha"
                                        value="{{ old('fecha') }}" required>
                                </div>
                                <div class="col-sm form-group">
                                    <label for="document_create_root" class="form-control-label">Archivo (.pdf)</label>
                                    <input type="file" class="form-control-file" id="document_create_root"
                                        name="root" accept=".pdf,application/pdf">
                                    <small class="form-text text-muted">Opcional. Tamaño máximo: 15 MB.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-white has-shadow">
                            <h5 class="mb-3">Campos adicionales</h5>
                            <div class="row" id="document_create_campos_container">
                                <div class="col">
                                    <p class="text-center">Seleccione un tipo de documento</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="documentEditModal" tabindex="-1" role="dialog" aria-labelledby="documentEditLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentEditLabel">Editar documento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="documentEditForm" action="{{ route('documents.update', ['document' => 'DOC_ID']) }}"
                    data-action-template="{{ route('documents.update', ['document' => 'DOC_ID']) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="edit">
                    <input type="hidden" name="edit_document_id" id="edit_document_id"
                        value="{{ old('edit_document_id') }}">
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
                        <div class="row mb-4">
                            <div class="col-12 col-lg-4">
                                <label for="document_edit_type" class="form-control-label">Tipo de Documento</label>
                                <select id="document_edit_type" name="document_type_id" class="form-control" disabled>
                                    <option value="">Seleccione un tipo</option>
                                    @foreach ($allDocumentTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="p-4 bg-white has-shadow">
                            <h5 class="mb-3">Campos generales</h5>
                            <div class="row">
                                <div class="col-sm form-group">
                                    <label for="document_edit_n_documento" class="form-control-label">N° de
                                        Documento</label>
                                    <input type="text" name="n_documento" id="document_edit_n_documento"
                                        class="form-control" value="{{ old('n_documento') }}" required>
                                </div>
                                <div class="col-sm form-group">
                                    <label for="document_edit_asunto" class="form-control-label">Asunto</label>
                                    <input type="text" class="form-control" id="document_edit_asunto" name="asunto"
                                        value="{{ old('asunto') }}" required>
                                </div>
                                <div class="col-sm form-group">
                                    <label class="form-control-label">Archivo Actual</label>
                                    <div>
                                        <a id="document_edit_file_link" href="#" target="_blank"
                                            class="btn btn-warning">Ver Archivo Actual</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm form-group">
                                    @can('documents.upload')
                                        <label for="document_edit_root" class="form-control-label">Nuevo Archivo
                                            (.pdf)</label>
                                        <input type="file" class="form-control-file" id="document_edit_root"
                                            name="root" accept=".pdf">
                                        <small class="form-text text-muted">Deja este campo vacío si no deseas reemplazar
                                            el archivo actual.</small>
                                    @else
                                        <label class="form-control-label">Permiso de archivo</label>
                                        <p class="form-control-plaintext text-muted mb-0">No tienes permiso para actualizar
                                            el archivo.</p>
                                    @endcan
                                </div>
                                <div class="col-sm form-group">
                                    <label for="document_edit_folios" class="form-control-label">Folios</label>
                                    <input type="text" class="form-control" id="document_edit_folios" name="folios"
                                        value="{{ old('folios') }}">
                                </div>
                                <div class="col-sm form-group">
                                    <label for="document_edit_fecha" class="form-control-label">Fecha</label>
                                    <input type="date" name="fecha" id="document_edit_fecha" class="form-control"
                                        value="{{ old('fecha') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-white has-shadow">
                            <h5 class="mb-3">Campos adicionales</h5>
                            <div class="row" id="document_edit_campos_container">
                                <div class="col">
                                    <p class="text-center">Seleccione un tipo de documento</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="documentShowModal" tabindex="-1" role="dialog" aria-labelledby="documentShowLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentShowLabel">Detalle del documento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>N°:</strong> <span id="document_show_numero">-</span>
                                </li>
                                <li class="list-group-item"><strong>Asunto:</strong> <span
                                        id="document_show_asunto">—</span>
                                </li>
                                <li class="list-group-item"><strong>Folios:</strong> <span
                                        id="document_show_folios">—</span>
                                </li>
                                <li class="list-group-item"><strong>Tipo:</strong> <span id="document_show_tipo">—</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Fecha:</strong> <span
                                        id="document_show_fecha">—</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Campos adicionales</h6>
                        </div>
                        <div class="card-body">
                            <ul id="document_show_campos" class="mb-0"></ul>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Vista previa</h6>
                        </div>
                        <div class="card-body">
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe id="document_show_iframe" src="" frameborder="0" width="100%"
                                    height="520px" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="documents-page-data" class="d-none" data-create-document-types='@json($createDocumentTypes)'
        data-all-document-types='@json($allDocumentTypes)' data-old-modal='@json(old('_modal'))'
        data-old-edit-id='@json(old('edit_document_id'))'></div>
@endsection
