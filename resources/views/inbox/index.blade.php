@extends('layouts.app')

@section('title', 'Bandeja de entrada')

@push('styles')
    <style>
        .inbox-page {
            --inbox-primary: #0f4c81;
            --inbox-primary-soft: #e8f2fb;
            --inbox-success-soft: #e7f6ee;
            --inbox-warning-soft: #fff4df;
            --inbox-neutral-soft: #f4f6f9;
        }

        .inbox-page .inbox-hero {
            background: linear-gradient(135deg, #0f4c81 0%, #1c6aa8 100%);
            color: #fff;
            border: 0;
            border-radius: 14px;
            overflow: hidden;
        }

        .inbox-page .inbox-hero .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.2);
        }

        .inbox-page .metric-card {
            border: 0;
            border-radius: 12px;
        }

        .inbox-page .metric-card .metric-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-right: 12px;
        }

        .inbox-page .metric-card.success .metric-icon {
            background: var(--inbox-success-soft);
            color: #20874f;
        }

        .inbox-page .metric-card.warning .metric-icon {
            background: var(--inbox-warning-soft);
            color: #b77b0a;
        }

        .inbox-page .metric-card.primary .metric-icon {
            background: var(--inbox-primary-soft);
            color: var(--inbox-primary);
        }

        .inbox-page .inbox-filter-card {
            border: 0;
            border-radius: 12px;
        }

        .inbox-page .inbox-filter-card .form-control {
            border-radius: 9px;
            min-height: 42px;
        }

        .inbox-page .table-card {
            border: 0;
            border-radius: 12px;
            overflow: hidden;
        }

        .inbox-page .inbox-table thead th {
            border-top: 0;
            background: #f8fafc;
            color: #4c5a67;
            font-size: 12px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .inbox-page .inbox-table tbody tr {
            transition: background-color 0.18s ease;
        }

        .inbox-page .inbox-table tbody tr:hover {
            background-color: #fbfdff;
        }

        .inbox-page .subject-main {
            max-width: 340px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .inbox-page .action-row .btn {
            margin-right: 8px;
            margin-bottom: 8px;
        }

        .inbox-page .action-row .btn:last-child {
            margin-right: 0;
        }

        .inbox-page .empty-state {
            min-height: 250px;
            border: 0;
            border-radius: 12px;
        }

        .inbox-page .empty-state .empty-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: var(--inbox-neutral-soft);
            color: #6d7b88;
            font-size: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        @media (max-width: 767.98px) {
            .inbox-page .subject-main {
                max-width: 180px;
            }
        }
    </style>
@endpush

@section('content')
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Bandeja de Entrada</h2>
        </div>
    </header>

    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Bandeja de entrada</li>
        </ul>
    </div>

    <section class="forms inbox-page">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card inbox-hero has-shadow mb-4">
                <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <div class="hero-chip mb-2">
                            <i class="fa-solid fa-inbox"></i>
                            Flujo de ingreso de bloques
                        </div>
                        <h4 class="mb-1 text-white">Gestion de bandeja de entrada</h4>
                        <p class="mb-0 text-white-50" style="opacity: .88;">Busca, asigna almacenamiento y registra archivos
                            en un solo lugar.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="hero-chip">
                            <i class="fa-solid fa-list"></i>
                            {{ $documents->total() }} registros
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-12 col-md-4 mb-3">
                    <div class="card metric-card primary has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <span class="metric-icon"><i class="fa-solid fa-inbox"></i></span>
                            <div>
                                <div class="text-muted small">Bloques en bandeja</div>
                                <div class="h4 mb-0">{{ $documents->total() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="card metric-card success has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <span class="metric-icon"><i class="fa-solid fa-box-archive"></i></span>
                            <div>
                                <div class="text-muted small">Bloques atendidos</div>
                                <div class="h4 mb-0">{{ $attendedBlocksCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="card metric-card warning has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <span class="metric-icon"><i class="fa-solid fa-box-open"></i></span>
                            <div>
                                <div class="text-muted small">Bloques sin atender</div>
                                <div class="h4 mb-0">{{ $unattendedBlocksCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card inbox-filter-card has-shadow mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <h5 class="mb-2 mb-md-0">Filtros de busqueda</h5>
                        @if (request()->filled('search') || request()->filled('area_id') || request()->filled('periodo'))
                            <a href="{{ route('inbox.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-rotate-left mr-1"></i> Limpiar filtros
                            </a>
                        @endif
                    </div>

                    <form action="{{ route('inbox.index') }}" method="GET">
                        <div class="form-row align-items-end">
                            <div class="form-group col-12 col-md-5">
                                <label for="search">Asunto</label>
                                <input type="text" name="search" id="search" class="form-control"
                                    value="{{ request('search') }}" placeholder="Ej. Informe, solicitud, memo...">
                            </div>
                            <div class="form-group col-12 col-md-3">
                                <label for="area_id">Area</label>
                                <select name="area_id" id="area_id" class="form-control">
                                    <option value="">Todas las areas</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-2">
                                <label for="periodo">Periodo</label>
                                <select name="periodo" id="periodo" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach ($periodos as $periodo)
                                        <option value="{{ $periodo }}"
                                            {{ request('periodo') == $periodo ? 'selected' : '' }}>
                                            {{ $periodo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if ($documents->isEmpty())
                <div class="card empty-state has-shadow d-flex align-items-center justify-content-center text-center">
                    <div class="card-body">
                        <span class="empty-icon"><i class="fa-solid fa-folder-open"></i></span>
                        <h5 class="mb-2">No hay bloques en la bandeja</h5>
                        <p class="text-muted mb-0">Ajusta los filtros o espera nuevos ingresos para continuar.</p>
                    </div>
                </div>
            @else
                <div class="card table-card has-shadow">
                    <div class="table-responsive">
                        <table class="table inbox-table mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Asunto</th>
                                    <th>Folios</th>
                                    <th>Periodo</th>
                                    <th>Mes</th>
                                    <th>Area</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $document)
                                    <tr>
                                        <td class="text-muted">#{{ $document->id }}</td>
                                        <td>
                                            <div class="font-weight-bold subject-main" title="{{ $document->asunto }}">
                                                {{ $document->asunto }}
                                            </div>
                                            <small class="text-muted">Bloque N.&ordm; {{ $document->n_bloque }}</small>
                                        </td>
                                        <td>{{ $document->folios }}</td>
                                        <td>{{ $document->fecha->year }}</td>
                                        <td>{{ ucfirst($document->fecha->translatedFormat('F')) }}</td>
                                        <td>{{ $document->user->group->areaGroupType->area->descripcion ?? 'Sin area' }}
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">Sin atender</span>
                                        </td>
                                        <td>
                                            <div class="action-row d-flex flex-wrap">
                                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#editStorageModal-{{ $document->id }}">
                                                    <i class="fa-solid fa-boxes-stacked mr-1"></i> Asignar
                                                </button>
                                                @can('blocks.upload')
                                                    <button class="btn btn-secondary btn-sm" data-toggle="modal"
                                                        data-target="#uploadFileModal-{{ $document->id }}">
                                                        <i class="fa-solid fa-upload mr-1"></i> Subir archivo
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editStorageModal-{{ $document->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="editStorageModalLabel-{{ $document->id }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form action="{{ route('inbox.updateStorage', $document->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="editStorageModalLabel-{{ $document->id }}">
                                                            Asignar almacenamiento
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Cerrar">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-muted small mb-3">Selecciona seccion, andamio y
                                                            caja.</p>
                                                        <div class="form-group">
                                                            <label for="section-{{ $document->id }}">Seccion</label>
                                                            <select class="form-control section-select"
                                                                id="section-{{ $document->id }}" name="n_section"
                                                                required>
                                                                <option value="">Seleccione una seccion</option>
                                                                @foreach ($sections as $section)
                                                                    <option value="{{ $section->id }}">
                                                                        {{ $section->n_section }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="andamio-{{ $document->id }}">Andamio</label>
                                                            <select class="form-control andamio-select"
                                                                id="andamio-{{ $document->id }}" name="n_andamio"
                                                                required>
                                                                <option value="">Seleccione un andamio</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group mb-0">
                                                            <label for="box-{{ $document->id }}">Caja</label>
                                                            <select class="form-control box-select"
                                                                id="box-{{ $document->id }}" name="n_box" required>
                                                                <option value="">Seleccione una caja</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    @can('blocks.upload')
                                        <div class="modal fade" id="uploadFileModal-{{ $document->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="uploadFileModalLabel-{{ $document->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <form action="{{ route('blocks.upload', $document->id) }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="_modal" value="upload">
                                                    <input type="hidden" name="upload_block_id"
                                                        value="{{ $document->id }}">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="uploadFileModalLabel-{{ $document->id }}">Subir archivo
                                                                del
                                                                bloque</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Cerrar">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @if ($errors->any() && old('_modal') === 'upload' && (int) old('upload_block_id') === $document->id)
                                                                <div class="alert alert-danger">
                                                                    <ul class="mb-0">
                                                                        @foreach ($errors->all() as $error)
                                                                            <li>{{ $error }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif

                                                            <p class="mb-3">Bloque N.&ordm;
                                                                <strong>{{ $document->n_bloque }}</strong></p>
                                                            <div class="form-group mb-0">
                                                                <label for="upload_root_{{ $document->id }}">Archivo
                                                                    (.pdf)</label>
                                                                <input type="file" id="upload_root_{{ $document->id }}"
                                                                    class="form-control-file" name="root" accept=".pdf"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                data-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary">Subir
                                                                archivo</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endcan
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 pt-3">
                        {{ $documents->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>

        <script>
            $(document).ready(function() {
                const andamios = @json($andamios);
                const boxes = @json($boxes);

                $(".section-select").on("change", function() {
                    const sectionId = $(this).val();
                    const modal = $(this).closest(".modal");
                    const andamioSelect = modal.find(".andamio-select");
                    const boxSelect = modal.find(".box-select");

                    boxSelect.html("<option value=''>Seleccione una caja</option>");

                    if (sectionId) {
                        const filteredAndamios = andamios.filter(a => a.section_id == sectionId);

                        if (filteredAndamios.length) {
                            andamioSelect.html("<option value=''>Seleccione un andamio</option>");
                            filteredAndamios.forEach(andamio => {
                                andamioSelect.append(
                                    `<option value="${andamio.id}">${andamio.n_andamio}</option>`);
                            });
                            andamioSelect.prop('disabled', false);
                        } else {
                            andamioSelect.html("<option value=''>No hay andamios para esta seccion</option>");
                            andamioSelect.prop('disabled', true);
                        }
                    } else {
                        andamioSelect.html("<option value=''>Seleccione un andamio</option>");
                        andamioSelect.prop('disabled', true);
                        boxSelect.prop('disabled', true);
                    }
                });

                $(".andamio-select").on("change", function() {
                    const andamioId = $(this).val();
                    const modal = $(this).closest(".modal");
                    const boxSelect = modal.find(".box-select");

                    if (andamioId) {
                        const filteredBoxes = boxes.filter(b => b.andamio_id == andamioId);

                        if (filteredBoxes.length) {
                            boxSelect.html("<option value=''>Seleccione una caja</option>");
                            filteredBoxes.forEach(box => {
                                boxSelect.append(`<option value="${box.id}">${box.n_box}</option>`);
                            });
                            boxSelect.prop('disabled', false);
                        } else {
                            boxSelect.html("<option value=''>No hay cajas para este andamio</option>");
                            boxSelect.prop('disabled', true);
                        }
                    } else {
                        boxSelect.html("<option value=''>Seleccione una caja</option>");
                        boxSelect.prop('disabled', true);
                    }
                });

                const oldModal = @json(old('_modal'));
                const oldUploadBlockId = @json(old('upload_block_id'));
                if (oldModal === 'upload' && oldUploadBlockId) {
                    $(`#uploadFileModal-${oldUploadBlockId}`).modal('show');
                }
            });
        </script>
    </section>
@endsection
