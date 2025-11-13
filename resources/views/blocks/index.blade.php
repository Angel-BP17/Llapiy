@extends('layouts.app')

@section('title', 'Bloques')
@section('content')
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
    <section class="forms">
        <div class="container-fluid">
            <!-- Mostrar mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-md-12">
                    <a class="ml-right btn btn-success" href="{{ route('blocks.create') }}"><i
                            class="fa-regular fa-file"></i> Ingresar Bloque</a>
                </div>
            </div>

            <!-- Filtros de búsuqeda -->
            <h4>Buscar</h4>
            <form method="GET" action="{{ route('blocks.index') }}">
                <div class="row mt-3">
                    <div class="col-md mb-3">
                        <label for="asunto" class="sr-only">Asunto</label>
                        <input type="text" name="asunto" id="asunto" class="form-control"
                            value="{{ request('asunto') }}" placeholder="Ingrese el asunto">
                    </div>
                </div>
                <div class="row">
                    @if (Auth::user()->isAdminOrManager())
                        <div class="col-md-4 mb-3">
                            <label for="area_id" class="sr-only">Área</label>
                            <select name="area_id" id="area_id" class="form-control">
                                <option value="">Área</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}"
                                        {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-4 mb-3">
                        <label for="year" class="sr-only">Periodo</label>
                        <select name="year" id="year" class="form-control">
                            <option value="">Periodo</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="month" class="sr-only">Mes</label>
                        <select name="month" id="month" class="form-control">
                            <option value="">Mes</option>
                            @foreach ([1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'] as $key => $month)
                                <option value="{{ $key }}" {{ request('month') == $key ? 'selected' : '' }}>
                                    {{ $month }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col mb-3">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i>
                            Buscar</button>
                        <a href="{{ route('blocks.index') }}" class="btn btn-secondary"><i class="fa-solid fa-x"></i></a>
                    </div>

                    <!-- Botón para generar PDF con los mismos filtros -->
                    @php
                        // Construir la URL para PDF con los mismos parámetros de búsqueda
                        $pdfUrl = route('blocks.pdfReport', request()->all());
                    @endphp

                    <div class="col-auto">
                        @if ($blocks->count() < 1)
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                                title="Para generar un reporte debe haber ingresado al menos 1 bloque">
                                <a href="{{ $pdfUrl }}" class="btn btn-danger mb-3 disabled" target="_blank">
                                    <i class="fa-solid fa-file-pdf"></i> Generar Reporte
                                </a>
                            </span>
                        @else
                            <a href="{{ $pdfUrl }}" class="btn btn-danger mb-3 " target="_blank">
                                <i class="fa-solid fa-file-pdf"></i> Generar Reporte
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="m-2 row bg-white has-shadow">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>N° de bloque</th>
                                <th>Asunto</th>
                                <th>Folios</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($blocks as $index => $block)
                                <tr>
                                    <td>{{ $blocks->firstItem() + $index }}</td>
                                    <td>{{ $block->n_bloque }}</td>
                                    <td>{{ $block->asunto }}</td>
                                    <td>{{ $block->folios }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('blocks.show', $block) }}" class="btn btn-info btn-sm">
                                                <i class="fa-solid fa-eye"></i> Ver
                                            </a>
                                            <a href="{{ route('blocks.edit', $block) }}" class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-pen"></i> Editar
                                            </a>
                                            <form action="{{ route('blocks.destroy', $block) }}" method="POST">
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
                                    <td colspan="6" class="text-center">No se encontraron bloques.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $blocks->links() }}
            </div>

        </div>
    </section>
@endsection
