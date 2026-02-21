@extends('layouts.app')

@section('title', 'Andamios')
@section('content')
    @vite('resources/js/andamios/index.js')
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Andamios</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Secciones</li>
            <li class="breadcrumb-item active">Andamios</li>
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

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card module-hero has-shadow mb-4">
                <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <div class="module-hero-chip mb-2">
                            <i class="fa-solid fa-grip-vertical"></i> Distribucion por seccion
                        </div>
                        <h4 class="mb-1 text-white">Andamios de la seccion {{ $section->n_section }}</h4>
                        <p class="mb-0 module-hero-text text-white-50">Administra andamios y navega rapidamente hacia las
                            cajas de almacenamiento.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $andamios->count() }} registros
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-violet"><i class="fa-solid fa-grip-vertical"></i></div>
                            <div>
                                <div class="text-muted small">Andamios registrados</div>
                                <div class="h4 mb-0">{{ $andamios->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <a href="{{ route('sections.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left-long"></i> Volver
                    </a>
                </div>
            </div>

            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Buscar</h5>
                    <form id="andamio-search-form" method="GET"
                        action="{{ route('sections.andamios.index', ['section' => $section->id]) }}"
                        class="row align-items-end">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="andamio-search" class="sr-only">Buscar andamio</label>
                            <input type="text" name="search" id="andamio-search" class="form-control"
                                value="{{ request('search') }}" placeholder="Número o descripción de andamio">
                        </div>
                        <div class="col-12 col-md-auto mb-3">
                            <button type="button" id="andamio-search-clear" class="btn btn-secondary">
                                <i class="fa-solid fa-x"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card module-panel bg-white has-shadow mb-4">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0">Registrar nuevo andamio</h5>
                </div>
                <div class="card-body">
                    <form class="form-inline" action="{{ route('sections.andamios.store', $section->id) }}" method="POST">
                        @csrf
                        <div class="form-group mr-2 mb-2">
                            <label for="n_andamio" class="sr-only">Numero de andamio</label>
                            <input type="number" name="n_andamio" id="n_andamio" class="form-control"
                                placeholder="Numero de andamio" required>
                        </div>
                        <div class="form-group mr-2 mb-2">
                            <label for="descripcion" class="sr-only">Descripcion</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control"
                                placeholder="Descripcion" required>
                        </div>
                        <div class="form-group mb-2">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>
                                Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row" id="andamios-list">
                @forelse ($andamios as $andamio)
                    <div class="col-md-4 mb-4 js-storage-item">
                        <div class="card module-panel bg-white has-shadow h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Andamio {{ $andamio->n_andamio }}</h5>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('sections.andamios.boxes.index', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                                    class="mb-3 btn btn-info btn-sm"><i class="fa-solid fa-arrows-down-to-line"></i>
                                    Ver cajas <i class="fa-solid fa-box-open"></i></a>
                                <p class="card-text mb-0">Descripcion: {{ $andamio->descripcion }}</p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#editAndamioModal{{ $andamio->id }}"><i class="fa-solid fa-pen"></i>
                                    Editar
                                </button>
                                <form
                                    action="{{ route('sections.andamios.destroy', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                                    method="POST" onsubmit="return confirm('Seguro de eliminar este andamio?')"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        @if ($andamio->boxes_count > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene cajas asociadas" @endif><i
                                            class="fa-solid fa-trash"></i> Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="editAndamioModal{{ $andamio->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="editAndamioModalLabel{{ $andamio->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editAndamioModalLabel{{ $andamio->id }}">
                                        Editar andamio</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form
                                    action="{{ route('sections.andamios.update', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="n_andamio">Numero de andamio</label>
                                            <input type="number" name="n_andamio" id="n_andamio" class="form-control"
                                                value="{{ $andamio->n_andamio }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="descripcion">Descripcion</label>
                                            <input type="text" name="descripcion" id="descripcion"
                                                class="form-control" value="{{ $andamio->descripcion }}" required>
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
                @empty
                    <div class="col-12">
                        <div class="module-empty has-shadow d-flex align-items-center justify-content-center text-center">
                            <div>
                                <span class="module-empty-icon"><i class="fa-solid fa-folder-open"></i></span>
                                <p class="mb-0">No hay andamios registrados en esta seccion.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
                @if ($andamios->count() > 0)
                    <div class="col-12" id="andamios-dynamic-empty" style="display:none;">
                        <div class="module-empty has-shadow d-flex align-items-center justify-content-center text-center">
                            <div>
                                <span class="module-empty-icon"><i class="fa-solid fa-folder-open"></i></span>
                                <p class="mb-0">No se encontraron andamios con ese criterio.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
