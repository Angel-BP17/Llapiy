@extends('layouts.app')

@section('title', 'Bandeja de entrada')
@section('content')
    <!-- Page Header-->
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
    <section class="forms">
        <!-- Module content-->
        <div class="container-fluid">
            <!-- Mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Formulario de búsqueda y filtros -->
            <h4>Buscar</h4>
            <form action="{{ route('inbox.index') }}" method="GET" class="mb-4">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="search" class="sr-only">Asunto</label>
                        <input type="text" name="search" id="search" class="form-control"
                            value="{{ request('search') }}" placeholder="Asunto">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="area_id" class="sr-only">Área</label>
                        <select name="area_id" id="area_id" class="form-control">
                            <option value="">Seleccionar área</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->descripcion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="periodo" class="sr-only">Periodo</label>
                        <select name="periodo" id="periodo" class="form-control">
                            <option value="">Todos los periodos</option>
                            @foreach ($periodos as $periodo)
                                <option value="{{ $periodo }}" {{ request('periodo') == $periodo ? 'selected' : '' }}>
                                    {{ $periodo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block"><i
                                class="fa-solid fa-magnifying-glass"></i> Buscar</button>
                    </div>
                </div>
            </form>

            <!-- Tabla de documentos -->
            @if ($documents->isEmpty())
                <p class="text-center">No hay documentos en la bandeja de entrada.</p>
            @else
                <div class="m-2 row bg-white has-shadow">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Asunto</th>
                                    <th>Folios</th>
                                    <th>Periodo</th>
                                    <th>Mes</th>
                                    <th>Área</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $document)
                                    <tr>
                                        <td>{{ $document->id }}</td>
                                        <td>{{ $document->asunto }}</td>
                                        <td>{{ $document->folios }}</td>
                                        <td>{{ $document->fecha->year }}</td>
                                        <td>{{ ucfirst($document->fecha->translatedFormat('F')) }}</td>
                                        <td>{{ $document->user->group->areaGroupType->area->descripcion ?? 'Sin área' }}
                                        </td>
                                        <td>
                                            <!-- Botón para abrir el modal -->
                                            <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                data-target="#editStorageModal-{{ $document->id }}">
                                                <i class="fa-solid fa-boxes-stacked"></i> Asignar
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal -->
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
                                                        <h5 class="modal-title">Editar Almacenamiento</h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Sección -->
                                                        <div class="form-group">
                                                            <label for="section-{{ $document->id }}">Sección</label>
                                                            <select class="form-control section-select"
                                                                id="section-{{ $document->id }}" name="n_section" required>
                                                                <option value="">Seleccione una sección</option>
                                                                @foreach ($sections as $section)
                                                                    <option value="{{ $section->id }}">
                                                                        {{ $section->n_section }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <!-- Andamio -->
                                                        <div class="form-group">
                                                            <label for="andamio-{{ $document->id }}">Andamio</label>
                                                            <select class="form-control andamio-select"
                                                                id="andamio-{{ $document->id }}" name="n_andamio" required>
                                                                <option value="">Seleccione un andamio</option>
                                                            </select>
                                                        </div>

                                                        <!-- Caja -->
                                                        <div class="form-group">
                                                            <label for="box-{{ $document->id }}">Caja</label>
                                                            <select class="form-control box-select"
                                                                id="box-{{ $document->id }}" name="n_box" required>
                                                                <option value="">Seleccione una caja</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cerrar</button>
                                                        <button type="submit" class="btn btn-primary">Guardar
                                                            Cambios</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginación -->
                    {{ $documents->links() }}
            @endif
        </div>
        <script>
            $(document).ready(function() {
                // Datos precargados desde backend, asegúrate que están disponibles como JSON en la vista:
                const andamios = @json($andamios);
                const boxes = @json($boxes);

                $(".section-select").on("change", function() {
                    const sectionId = $(this).val();
                    const modal = $(this).closest(".modal");
                    const andamioSelect = modal.find(".andamio-select");
                    const boxSelect = modal.find(".box-select");

                    boxSelect.html("<option value=''>Seleccione una caja</option>");

                    if (sectionId) {
                        // Filtrar andamios que pertenecen a la sección seleccionada
                        const filteredAndamios = andamios.filter(a => a.section_id == sectionId);

                        if (filteredAndamios.length) {
                            andamioSelect.html("<option value=''>Seleccione un andamio</option>");
                            filteredAndamios.forEach(andamio => {
                                andamioSelect.append(
                                    `<option value="${andamio.id}">${andamio.n_andamio}</option>`);
                            });
                            andamioSelect.prop('disabled', false);
                        } else {
                            andamioSelect.html("<option value=''>No hay andamios para esta sección</option>");
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
                        // Filtrar cajas que pertenecen al andamio seleccionado
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
            });
        </script>
    </section>
@endsection
