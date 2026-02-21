@extends('layouts.app')

@section('title', 'Archivos')
@section('content')
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Archivos</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Almacen</li>
            <li class="breadcrumb-item active">Secciones</li>
            <li class="breadcrumb-item active">Andamios</li>
            <li class="breadcrumb-item active">Cajas</li>
            <li class="breadcrumb-item active">Archivos</li>
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
                            <i class="fa-solid fa-file-archive"></i> Inventario documental
                        </div>
                        <h4 class="mb-1 text-white">Archivos en caja {{ $box->n_box }}</h4>
                        <p class="mb-0 module-hero-text text-white-50">Visualiza documentos archivados y retira archivos
                            cuando sea necesario.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $blocks->total() }} registros
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-4 align-items-center">
                <div class="col-md-4 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-blue"><i class="fa-solid fa-file"></i></div>
                            <div>
                                <div class="text-muted small">Archivos en caja</div>
                                <div class="h4 mb-0">{{ $blocks->total() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <a href="{{ route('sections.andamios.boxes.index', ['section' => $section, 'andamio' => $andamio, 'box' => $box->id]) }}"
                        class="btn btn-secondary"><i class="fa-solid fa-arrow-left-long"></i> Volver</a>
                </div>
            </div>

            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Buscar</h5>
                    <form method="GET"
                        action="{{ route('sections.andamios.boxes.archivos.index', ['section' => $section, 'andamio' => $andamio, 'box' => $box->id]) }}"
                        class="row align-items-end">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="search" class="sr-only">Buscar archivo</label>
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="N. bloque, asunto, folios o periodo">
                        </div>
                        <div class="col-12 col-md-auto mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-magnifying-glass"></i> Buscar
                            </button>
                        </div>
                        <div class="col-12 col-md-auto mb-3">
                            <a href="{{ route('sections.andamios.boxes.archivos.index', ['section' => $section, 'andamio' => $andamio, 'box' => $box->id]) }}"
                                class="btn btn-secondary">
                                <i class="fa-solid fa-x"></i> Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card module-table-card bg-white has-shadow mb-4">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table module-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Asunto</th>
                                    <th>Folios</th>
                                    <th>Periodo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($blocks as $block)
                                    <tr>
                                        <td>{{ $block->id }}</td>
                                        <td>{{ $block->asunto }}</td>
                                        <td>{{ $block->folios }}</td>
                                        <td>{{ $block->periodo }}</td>
                                        <td>
                                            <a href="{{ route('blocks.index') }}" class="btn btn-info btn-sm"><i
                                                    class="fa-solid fa-eye"></i> Ver documento</a>
                                            <form style="display: inline-block;"
                                                action="{{ route('sections.andamios.boxes.archivos.move', [
                                                    'section' => $section,
                                                    'andamio' => $andamio,
                                                    'box' => $box->id,
                                                    'block' => $block,
                                                ]) }}"
                                                method="POST" onsubmit="return confirm('Seguro de retirar este archivo?')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm"><i
                                                        class="fa-solid fa-x"></i>
                                                    Retirar del almacen</button>
                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No hay archivos en este paquete.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $blocks->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
