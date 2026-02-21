@extends('layouts.app')

@section('title', 'Cajas')
@section('content')
    @vite('resources/js/boxes/index.js')
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Cajas</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Secciones</li>
            <li class="breadcrumb-item active">Andamios</li>
            <li class="breadcrumb-item active">Cajas</li>
        </ul>
    </div>

    <section class="forms module-ui">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card module-hero has-shadow mb-4">
                <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <div class="module-hero-chip mb-2">
                            <i class="fa-solid fa-box-open"></i> Gestion por andamio
                        </div>
                        <h4 class="mb-1 text-white">Cajas del andamio {{ $andamio->n_andamio }}</h4>
                        <p class="mb-0 module-hero-text text-white-50">Administra cajas y accede a los archivos almacenados
                            en cada una.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $boxes->count() }} registros
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-4 align-items-center">
                <div class="col-md-4 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-orange"><i class="fa-solid fa-box-open"></i></div>
                            <div>
                                <div class="text-muted small">Cajas registradas</div>
                                <div class="h4 mb-0">{{ $boxes->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <a href="{{ route('sections.andamios.index', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                        class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left-long"></i> Volver
                    </a>
                </div>
            </div>

            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Buscar</h5>
                    <form id="box-search-form" method="GET"
                        action="{{ route('sections.andamios.boxes.index', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                        class="row align-items-end">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="box-search" class="sr-only">Buscar caja</label>
                            <input type="text" name="search" id="box-search" class="form-control"
                                value="{{ request('search') }}" placeholder="Número de caja">
                        </div>
                        <div class="col-12 col-md-auto mb-3">
                            <button type="button" id="box-search-clear" class="btn btn-secondary">
                                <i class="fa-solid fa-x"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card module-panel bg-white has-shadow mb-4">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0">Registrar nueva caja</h5>
                </div>
                <div class="card-body">
                    <form class="form-inline"
                        action="{{ route('sections.andamios.boxes.store', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                        method="POST">
                        @csrf
                        <div class="form-group mr-2 mb-2">
                            <label for="n_box" class="sr-only">Numero de caja</label>
                            <input type="number" name="n_box" id="n_box" class="form-control" required
                                placeholder="Numero de caja">
                        </div>
                        <div class="form-group mb-2">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>
                                Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row" id="boxes-list">
                @foreach ($boxes as $box)
                    <div class="col-md-6 js-storage-item">
                        <div class="card module-panel bg-white has-shadow mb-4">
                            <div class="card-header">
                                <strong>Caja: {{ $box->n_box }}</strong>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('sections.andamios.boxes.archivos.index', ['section' => $section->id, 'andamio' => $andamio->id, 'box' => $box->id]) }}"
                                    class="btn btn-info btn-sm mb-2"><i class="fa-solid fa-file"></i> Ver archivos</a>
                                <p class="mb-0">{{ $box->descripcion }}</p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#editBoxModal{{ $box->id }}"><i class="fa-solid fa-pen"></i>
                                    Editar caja
                                </button>

                                <form
                                    action="{{ route('sections.andamios.boxes.destroy', ['section' => $section->id, 'andamio' => $andamio->id, 'box' => $box->id]) }}"
                                    method="POST" style="display: inline-block;"
                                    onsubmit="return confirm('Seguro de eliminar esta caja?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        @if ($box->blocks_count > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene cajas asociadas" @endif><i
                                            class="fa-solid fa-trash"></i>
                                        Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="editBoxModal{{ $box->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="editBoxModalLabel{{ $box->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editBoxModalLabel{{ $box->id }}">Editar caja</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form
                                    action="{{ route('sections.andamios.boxes.update', ['section' => $section->id, 'andamio' => $andamio->id, 'box' => $box->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="n_box">Numero de caja</label>
                                            <input type="number" name="n_box" class="form-control"
                                                value="{{ $box->n_box }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary"><i
                                                class="fa-solid fa-floppy-disk"></i> Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if ($boxes->count() > 0)
                    <div class="col-12" id="boxes-dynamic-empty" style="display:none;">
                        <div class="module-empty has-shadow d-flex align-items-center justify-content-center text-center">
                            <div>
                                <span class="module-empty-icon"><i class="fa-solid fa-folder-open"></i></span>
                                <p class="mb-0">No se encontraron cajas con ese criterio.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
