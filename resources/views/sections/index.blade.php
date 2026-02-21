@extends('layouts.app')

@section('title', 'Secciones')
@section('content')
    @vite('resources/js/sections/index.js')
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Secciones</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Secciones</li>
        </ul>
    </div>

    <section class="forms module-ui">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card module-hero has-shadow mb-4">
                <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <div class="module-hero-chip mb-2">
                            <i class="fa-solid fa-layer-group"></i> Estructura de almacen
                        </div>
                        <h4 class="mb-1 text-white">Gestion de secciones</h4>
                        <p class="mb-0 module-hero-text text-white-50">Organiza secciones para distribuir andamios, cajas y
                            archivos fisicos.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $sections->count() }} registros
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-blue"><i class="fa-solid fa-layer-group"></i></div>
                            <div>
                                <div class="text-muted small">Secciones registradas</div>
                                <div class="h4 mb-0">{{ $sections->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Buscar</h5>
                    <form id="section-search-form" method="GET" action="{{ route('sections.index') }}"
                        class="row align-items-end">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="search" class="sr-only">Buscar sección</label>
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Número o descripción de sección">
                        </div>
                        <div class="col-12 col-md-auto mb-3">
                            <button type="button" id="section-search-clear" class="btn btn-secondary">
                                <i class="fa-solid fa-x"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card module-panel bg-white has-shadow mb-4">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0">Registrar nueva seccion</h5>
                </div>
                <div class="card-body">
                    <form class="form-inline" method="POST" action="{{ route('sections.store') }}">
                        @csrf
                        <div class="form-group mr-2 mb-2">
                            <label for="n_section" class="sr-only">Numero de seccion</label>
                            <input type="number" name="n_section" class="form-control" min="1" step="1"
                                placeholder="Numero de seccion" required>
                        </div>
                        <div class="form-group mr-2 mb-2">
                            <label for="descripcion" class="sr-only">Descripcion</label>
                            <input type="text" name="descripcion" class="form-control" placeholder="Descripcion"
                                required>
                        </div>
                        <div class="form-group mb-2">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>
                                Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row" id="sections-list">
                @foreach ($sections as $section)
                    <div class="col-md-4 js-storage-item">
                        <div class="card module-panel bg-white has-shadow mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Seccion {{ $section->n_section }}</h5>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('sections.andamios.index', ['section' => $section->id]) }}"
                                    class="btn btn-info btn-sm mb-2"><i class="fa-solid fa-grip-vertical"></i> Ver
                                    andamios</a>
                                <p class="card-text mb-0">Descripcion: {{ $section->descripcion }}</p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#editModal{{ $section->id }}"><i class="fa-solid fa-pen"></i>
                                    Editar</button>
                                <form method="POST" action="{{ route('sections.destroy', ['section' => $section->id]) }}"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Seguro de eliminar esta seccion?')"
                                        @if ($section->andamios_count > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene andamios asociados" @endif><i
                                            class="fa-solid fa-trash"></i> Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col-12" id="sections-dynamic-empty" style="display:none;">
                    <div class="module-empty has-shadow d-flex align-items-center justify-content-center text-center">
                        <div>
                            <span class="module-empty-icon"><i class="fa-solid fa-folder-open"></i></span>
                            <p class="mb-0">No se encontraron secciones con ese criterio.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($sections as $section)
            <div class="modal fade" id="editModal{{ $section->id }}" tabindex="-1" role="dialog"
                aria-labelledby="editModalLabel{{ $section->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $section->id }}">Editar seccion</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('sections.update', ['section' => $section->id]) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="n_section">Numero de seccion</label>
                                    <input type="number" name="n_section" class="form-control"
                                        value="{{ $section->n_section }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion">Descripcion</label>
                                    <input type="text" name="descripcion" class="form-control"
                                        value="{{ $section->descripcion }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>
                                    Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

    </section>
@endsection
