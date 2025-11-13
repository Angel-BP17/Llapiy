@extends('layouts.app')

@section('title', 'Documentos')
@section('content')
    @vite(['resources/js/area_group_subgroup_selector.js'])
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
                    @if ($documentTypes->count() < 1)
                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" data-placement="right"
                            title="Para ingresar un documento, primero cree un tipo de documento en el módulo de Inf. adicional de documentos">
                            <a class="ml-right btn btn-success disabled" href="{{ route('documents.create') }}"><i
                                    class="fa-regular fa-file"></i>
                                Ingresar
                                Documento</a>
                        </span>
                    @else
                        <a class="ml-right btn btn-success" href="{{ route('documents.create') }}"><i
                                class="fa-regular fa-file"></i>
                            Ingresar
                            Documento</a>
                    @endif
                </div>
            </div>
            <div class="card bg-white has-shadow mb-4">
                <div class="card-body">
                    <h4>Buscar</h4>
                    <form method="GET" action="{{ route('documents.index') }}">
                        <div class="row mt-3">
                            <div class="col-md mb-3">
                                <label for="asunto" class="sr-only">Asunto</label>
                                <input type="text" name="asunto" id="asunto" class="form-control"
                                    value="{{ request('asunto') }}" placeholder="Ingrese el asunto">
                            </div>

                        </div>
                        <div class="row">
                            <!-- Filtros para administradores y encargados -->
                            @if (Auth::user()->isAdminOrManager())
                                <!-- Filtro dinámico de Áreas, grupos y subgrupos -->
                                <div class="col-md mb-3">
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
                                <div class="col-md mb-3">
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
                                <div class="col-md mb-3">
                                    <label for="subgroup_id" class="sr-only">Subgrupo</label>
                                    <select name="subgroup_id" id="subgroup_id" class="form-control">
                                        <option value="">-- Seleccionar Subgrupo</option>
                                        @foreach ($subgroups as $subgroup)
                                            {{ $subgroup->descripcion }}
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <!-- Filtro generales -->
                            <div class="col-md-4 mb-3">
                                <label for="document_type_id" class="sr-only">Tipo de Documento</label>
                                <select name="document_type_id" id="document_type_id" class="form-control">
                                    <option value="">-- Seleccionar Tipo de Documento --</option>
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
                            <div class="col-md-4 mb-3">
                                <label for="year" class="sr-only">Periodo</label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">-- Seleccionar Periodo --</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}"
                                            {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="month" class="sr-only">Mes</label>
                                <select name="month" id="month" class="form-control">
                                    <option value="">-- Seleccionar Mes --</option>
                                    @foreach ([1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'] as $key => $month)
                                        <option value="{{ $key }}"
                                            {{ request('month') == $key ? 'selected' : '' }}>
                                            {{ $month }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i>
                                    Aplicar filtros</button>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('documents.index') }}" class="btn btn-secondary"><i
                                        class="fa-solid fa-x"></i>
                                    Limpiar filtros</a>
                            </div>

                            <!-- Botón para generar PDF con los mismos filtros -->
                            @php
                                // Construir la URL para PDF con los mismos parámetros de búsqueda
                                $pdfUrl = route('documents.pdfReport', request()->all());
                            @endphp

                            <div class="col-auto">
                                @if ($documents->count() < 1)
                                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                                        title="Para generar un reporte debe haber ingresado al menos 1 documento">
                                        <a href="{{ $pdfUrl }}" class="btn btn-danger disabled" target="_blank">
                                            <i class="fa-solid fa-file-pdf"></i> Generar Reporte
                                        </a>
                                    </span>
                                @else
                                    <a href="{{ $pdfUrl }}" class="btn btn-danger" target="_blank">
                                        <i class="fa-solid fa-file-pdf"></i> Generar Reporte
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="m-2 row bg-white has-shadow">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>N° de documento</th>
                                <th>Asunto</th>
                                <th>Folios</th>
                                <th>Tipo de documento</th>
                                <th>Acciones</th>
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
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('documents.show', $document) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fa-solid fa-eye"></i> Ver
                                            </a>
                                            <a href="{{ route('documents.edit', $document) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-pen"></i> Editar
                                            </a>
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
    <script>
        window.areas = @json($areas);
        window.selectedAreaId = "{{ request('area_id') }}";
        window.selectedGroupId = "{{ request('group_id') }}";
        window.selectedSubgroupId = "{{ request('subgroup_id') }}";
    </script>
@endsection
