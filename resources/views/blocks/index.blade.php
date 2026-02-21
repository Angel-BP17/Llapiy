@extends('layouts.app')

@section('title', 'Bloques')
@section('content')
    @vite('resources/js/blocks/index.js')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Bloques</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Bloques</li>
        </ul>
    </div>

    <!-- Tabla de documentos -->
    <section class="forms module-ui">
        <div class="container-fluid">
            <!-- Mostrar mensaje de ÃƒÂ©xito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="card module-hero has-shadow mb-4">
                <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <div class="module-hero-chip mb-2">
                            <i class="fa-solid fa-boxes-stacked"></i> Control de bloques
                        </div>
                        <h4 class="mb-1 text-white">Gestión de bloques</h4>
                        <p class="mb-0 module-hero-text text-white-50">Monitorea bloques, aplica filtros y administra su
                            estado operativo.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $blocks->total() }} registros
                        </span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-blue"><i class="fa-regular fa-file"></i></div>
                            <div>
                                <div class="text-muted small">Total de bloques</div>
                                <div class="h4 mb-0">{{ $totalBlocks }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-violet"><i class="fa-solid fa-sitemap"></i></div>
                            <div>
                                <div class="text-muted small">Áreas registradas</div>
                                <div class="h4 mb-0">{{ $totalAreas }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-green"><i class="fa-solid fa-box-archive"></i></div>
                            <div>
                                <div class="text-muted small">Bloques atendidos</div>
                                <div class="h4 mb-0">{{ $attendedBlocksCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-orange"><i class="fa-solid fa-box-open"></i></div>
                            <div>
                                <div class="text-muted small">Bloques sin atender</div>
                                <div class="h4 mb-0">{{ $unattendedBlocksCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <button class="btn btn-success" type="button" data-toggle="modal" data-target="#blockCreateModal">
                        <i class="fa-regular fa-file"></i> Ingresar Bloque
                    </button>
                </div>
            </div>

            <!-- Filtros de busqueda -->
            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body py-3">
                    @php
                        $pdfUrl = route('blocks.pdfReport', request()->all());
                    @endphp
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3 rounded px-3 py-2"
                        style="background: linear-gradient(90deg, rgba(52, 152, 219, 0.14), rgba(241, 196, 15, 0.12)); border: 1px solid rgba(52, 152, 219, 0.22);">
                        <div>
                            <h5 class="mb-1"><i class="fa-solid fa-sliders mr-2"></i>Filtros de busqueda</h5>
                            <p class="text-muted small mb-0">Refina los resultados por asunto, area y periodo.</p>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('blocks.index') }}">
                        <div class="row">
                            <div class="col-12 col-lg-4 mb-2">
                                <label for="asunto" class="small text-muted mb-1">Asunto</label>
                                <input type="text" name="asunto" id="asunto" class="form-control"
                                    value="{{ request('asunto') }}" placeholder="Ej. informe, oficio, memo">
                            </div>
                            <div class="col-12 col-md-4 col-lg-2 mb-2">
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
                            <div class="col-12 col-md-4 col-lg-2 mb-2">
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
                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                <label for="year" class="small text-muted mb-1">Periodo</label>
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
                            <div class="col-12 col-md-4 col-lg-2 mb-2">
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
                            <p class="text-muted small mb-2 mb-md-0">Aplica filtros para ubicar bloques rapidamente.</p>
                            <div class="d-flex flex-column flex-sm-row">
                                <button type="submit" class="btn btn-primary btn-sm mr-sm-2 mb-2 mb-sm-0">
                                    <i class="fa-solid fa-filter"></i> Aplicar filtros
                                </button>
                                <a href="{{ route('blocks.index') }}"
                                    class="btn btn-outline-secondary btn-sm mr-sm-2 mb-2 mb-sm-0">
                                    <i class="fa-solid fa-rotate-left"></i> Limpiar
                                </a>
                                @if ($blocks->count() < 1)
                                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                                        title="Para generar un reporte debe haber ingresado al menos 1 bloque">
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
                                <th>N° de bloque</th>
                                <th>Asunto</th>
                                <th>Folios</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($blocks as $index => $block)
                                <tr>
                                    <td>{{ $blocks->firstItem() + $index }}</td>
                                    <td>{{ $block->n_bloque }}</td>
                                    <td>{{ $block->asunto }}</td>
                                    <td>{{ $block->folios }}</td>
                                    <td class="text-end">
                                        @php
                                            $blockData = [
                                                'id' => $block->id,
                                                'n_bloque' => $block->n_bloque,
                                                'asunto' => $block->asunto,
                                                'folios' => $block->folios,
                                                'rango_inicial' => $block->rango_inicial,
                                                'rango_final' => $block->rango_final,
                                                'fecha' => optional($block->fecha)->format('Y-m-d'),
                                                'user' => [
                                                    'name' => $block->user->name ?? null,
                                                    'last_name' => $block->user->last_name ?? null,
                                                ],
                                                'area' => $block->group?->areaGroupType?->area?->descripcion,
                                                'group' => $block->group?->descripcion,
                                                'subgroup' => $block->subgroup?->descripcion,
                                                'box' => $block->box
                                                    ? [
                                                        'section' => $block->box->andamio?->section?->n_section,
                                                        'andamio' => $block->box->andamio?->n_andamio,
                                                        'box' => $block->box?->n_box,
                                                    ]
                                                    : null,
                                                'root_url' => $block->root ? asset('storage/' . $block->root) : null,
                                            ];
                                        @endphp
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info btn-sm js-block-show"
                                                data-toggle="modal" data-target="#blockShowModal"
                                                data-block='@json($blockData)'>
                                                <i class="fa-solid fa-eye"></i> Ver
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm js-block-edit"
                                                data-toggle="modal" data-target="#blockEditModal"
                                                data-block='@json($blockData)'>
                                                <i class="fa-solid fa-pen"></i> Editar
                                            </button>
                                            <form action="{{ route('blocks.destroy', $block) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Ã‚Â¿Eliminar este documento? Se borrarÃƒÂ¡ permanentemente')">
                                                    <i class="fa-solid fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron bloques.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PaginaciÃƒÂ³n -->
            <div class="d-flex justify-content-center">
                {{ $blocks->links() }}
            </div>

        </div>
    </section>

    <!-- Modales -->
    <div class="modal fade" id="blockCreateModal" tabindex="-1" role="dialog" aria-labelledby="blockCreateLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="blockCreateLabel">Ingresar bloque</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('blocks.store') }}" method="POST" enctype="multipart/form-data">
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
                        <div class="row">
                            <div class="col-sm form-group">
                                <label for="block_create_n_bloque" class="form-control-label">N° de Documento</label>
                                <input type="text" name="n_bloque" id="block_create_n_bloque" class="form-control"
                                    value="{{ old('n_bloque') }}" required>
                            </div>
                            <div class="col-sm form-group">
                                <label for="block_create_asunto" class="form-control-label">Asunto</label>
                                <input type="text" class="form-control" id="block_create_asunto" name="asunto"
                                    value="{{ old('asunto') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm form-group">
                                <label for="block_create_folios" class="form-control-label">Folios</label>
                                <input type="text" class="form-control" id="block_create_folios" name="folios"
                                    value="{{ old('folios') }}">
                            </div>
                            <div class="col-sm form-group">
                                <label for="block_create_rango_inicial" class="form-control-label">Rango inicial</label>
                                <input type="number" min="1" step="1" class="form-control"
                                    id="block_create_rango_inicial" name="rango_inicial"
                                    value="{{ old('rango_inicial') }}" required>
                            </div>
                            <div class="col-sm form-group">
                                <label for="block_create_rango_final" class="form-control-label">Rango final</label>
                                <input type="number" min="1" step="1" class="form-control"
                                    id="block_create_rango_final" name="rango_final" value="{{ old('rango_final') }}"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label for="block_create_fecha" class="form-control-label">Fecha</label>
                                <input type="date" class="form-control" id="block_create_fecha" name="fecha"
                                    value="{{ old('fecha') }}" required>
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

    <div class="modal fade" id="blockEditModal" tabindex="-1" role="dialog" aria-labelledby="blockEditLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="blockEditLabel">Editar bloque</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="blockEditForm" action="{{ route('blocks.update', ['block' => 'BLOCK_ID']) }}"
                    data-action-template="{{ route('blocks.update', ['block' => 'BLOCK_ID']) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="edit">
                    <input type="hidden" name="edit_block_id" id="edit_block_id" value="{{ old('edit_block_id') }}">
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
                        <div class="row">
                            <div class="col-sm form-group">
                                <label for="block_edit_n_bloque" class="form-control-label">N° de Documento</label>
                                <input type="text" name="n_bloque" id="block_edit_n_bloque" class="form-control"
                                    value="{{ old('n_bloque') }}" required>
                            </div>
                            <div class="col-sm form-group">
                                <label for="block_edit_asunto" class="form-control-label">Asunto</label>
                                <input type="text" class="form-control" id="block_edit_asunto" name="asunto"
                                    value="{{ old('asunto') }}" required>
                            </div>
                            <div class="col-sm form-group">
                                <label class="form-control-label">Archivo Actual</label>
                                <div>
                                    <a id="block_edit_file_link" href="#" target="_blank"
                                        class="btn btn-warning">Ver Archivo Actual</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm form-group">
                                @can('blocks.upload')
                                    <label for="block_edit_root" class="form-control-label">Nuevo Archivo (.pdf)</label>
                                    <input type="file" class="form-control-file" id="block_edit_root" name="root"
                                        accept=".pdf">
                                    <small class="form-text text-muted">Deja este campo vacío si no deseas reemplazar el
                                        archivo actual.</small>
                                @else
                                    <label class="form-control-label">Permiso de archivo</label>
                                    <p class="form-control-plaintext text-muted mb-0">No tienes permiso para actualizar
                                        el archivo.</p>
                                @endcan
                            </div>
                            <div class="col-sm form-group">
                                <label for="block_edit_folios" class="form-control-label">Folios</label>
                                <input type="text" class="form-control" id="block_edit_folios" name="folios"
                                    value="{{ old('folios') }}">
                            </div>
                            <div class="col-sm form-group">
                                <label for="block_edit_fecha" class="form-control-label">Fecha</label>
                                <input type="date" name="fecha" id="block_edit_fecha" class="form-control"
                                    value="{{ old('fecha') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label for="block_edit_rango_inicial" class="form-control-label">Rango inicial</label>
                                <input type="number" min="1" step="1" class="form-control"
                                    id="block_edit_rango_inicial" name="rango_inicial"
                                    value="{{ old('rango_inicial') }}" required>
                            </div>
                            <div class="col-sm-4 form-group">
                                <label for="block_edit_rango_final" class="form-control-label">Rango final</label>
                                <input type="number" min="1" step="1" class="form-control"
                                    id="block_edit_rango_final" name="rango_final" value="{{ old('rango_final') }}"
                                    required>
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

    <div class="modal fade" id="blockShowModal" tabindex="-1" role="dialog" aria-labelledby="blockShowLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="blockShowLabel">Detalles del bloque</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Usuario:</strong>
                                    <span id="block_show_user">—</span>
                                </li>
                                <li class="list-group-item"><strong>N° de documento:</strong>
                                    <span id="block_show_numero">—</span>
                                </li>
                                <li class="list-group-item"><strong>Asunto:</strong> <span id="block_show_asunto">—</span>
                                </li>
                                <li class="list-group-item"><strong>Folios:</strong> <span id="block_show_folios">—</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Sección:</strong> <span
                                        id="block_show_section">—</span>
                                </li>
                                <li class="list-group-item"><strong>Andamio:</strong> <span
                                        id="block_show_andamio">—</span>
                                </li>
                                <li class="list-group-item"><strong>Caja:</strong> <span id="block_show_box">—</span>
                                </li>
                                <li class="list-group-item"><strong>Área:</strong> <span id="block_show_area">—</span>
                                </li>
                                <li class="list-group-item"><strong>Grupo:</strong> <span id="block_show_group">—</span>
                                </li>
                                <li class="list-group-item"><strong>Subgrupo:</strong> <span
                                        id="block_show_subgroup">—</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Vista previa del bloque</h6>
                        </div>
                        <div class="card-body">
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe id="block_show_iframe" src="" frameborder="0" width="100%"
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

    <div id="blocks-page-data" class="d-none" data-old-modal='@json(old('_modal'))'
        data-old-edit-id='@json(old('edit_block_id'))' data-old-edit-name='@json(old('asunto'))'
        data-old-edit-n_bloque='@json(old('n_bloque'))' data-old-edit-folios='@json(old('folios'))'
        data-old-edit-fecha='@json(old('fecha'))' data-old-edit-rango-inicial='@json(old('rango_inicial'))'
        data-old-edit-rango-final='@json(old('rango_final'))'></div>
@endsection
