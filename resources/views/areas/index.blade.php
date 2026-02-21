@extends('layouts.app')

@section('title', 'Areas')
@section('content')
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Areas</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Areas</li>
        </ul>
    </div>

    <section class="forms module-ui">
        <div class="container-fluid">
            @php
                $visibleAreas = $areas->where('descripcion', '!=', 'Todas');
                $oldModal = old('_modal');
                $oldEditAreaId = old('edit_area_id');
            @endphp

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
                            <i class="fa-solid fa-building"></i> Estructura organizacional
                        </div>
                        <h4 class="mb-1 text-white">Gestion de areas</h4>
                        <p class="mb-0 module-hero-text text-white-50">Administra areas y su relacion con grupos y
                            subgrupos.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $visibleAreas->count() }} registros
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-blue"><i class="fa-solid fa-building"></i></div>
                            <div>
                                <div class="text-muted small">Areas registradas</div>
                                <div class="h4 mb-0">{{ $visibleAreas->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-start align-items-center mb-3">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#areaCreateModal">
                        <i class="icon-interface-windows"></i> Crear nueva area
                    </button>
                </div>
            </div>

            <div class="row">
                @forelse ($visibleAreas as $area)
                    <div class="col-md-6">
                        <div class="card module-panel bg-white has-shadow mb-4">
                            <div
                                class="card-header d-flex justify-content-between align-items-center text-white bg-secondary">
                                <h5 class="mb-0">{{ $area->descripcion }} ({{ $area->abreviacion }})</h5>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('areas.show', $area->id) }}" class="btn btn-info">
                                    <i class="fa-solid fa-people-group"></i> Ver grupos y subgrupos
                                </a>
                            </div>
                            <div class="card-footer text-right bg-white border-0">
                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                    data-target="#areaEditModal{{ $area->id }}">
                                    Editar area
                                </button>
                                <form action="{{ route('areas.destroy', $area->id) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Eliminar esta area y sus datos relacionados?')"
                                        @if ($area->groups_count > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene grupos asociados" @endif>
                                        Eliminar area
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="module-empty has-shadow d-flex align-items-center justify-content-center text-center">
                            <div>
                                <span class="module-empty-icon"><i class="fa-solid fa-folder-open"></i></span>
                                <p class="mb-0">No se encontraron areas registradas.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="modal fade" id="areaCreateModal" tabindex="-1" role="dialog" aria-labelledby="areaCreateLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="areaCreateLabel">Crear area</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('areas.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_modal" value="create">
                            <div class="modal-body">
                                @if ($errors->any() && $oldModal === 'create')
                                    <div class="alert alert-danger">
                                        <strong>Revisa los campos:</strong>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="create_descripcion">Descripcion del area</label>
                                    <input type="text" name="descripcion" id="create_descripcion" class="form-control"
                                        value="{{ old('descripcion') }}" required>
                                </div>
                                <div class="form-group mb-0">
                                    <label for="create_abreviacion">Abreviacion</label>
                                    <input type="text" name="abreviacion" id="create_abreviacion" class="form-control"
                                        value="{{ old('abreviacion') }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @foreach ($visibleAreas as $area)
                @php
                    $isCurrentEditAreaError = $oldModal === 'edit' && (int) $oldEditAreaId === $area->id;
                    $editDescripcion = $isCurrentEditAreaError ? old('descripcion') : $area->descripcion;
                    $editAbreviacion = $isCurrentEditAreaError ? old('abreviacion') : $area->abreviacion;
                @endphp
                <div class="modal fade" id="areaEditModal{{ $area->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="areaEditLabel{{ $area->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="areaEditLabel{{ $area->id }}">Editar area</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('areas.update', $area->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_modal" value="edit">
                                <input type="hidden" name="edit_area_id" value="{{ $area->id }}">
                                <div class="modal-body">
                                    @if ($errors->any() && $isCurrentEditAreaError)
                                        <div class="alert alert-danger">
                                            <strong>Revisa los campos:</strong>
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="edit_descripcion_{{ $area->id }}">Descripcion del area</label>
                                        <input type="text" name="descripcion" id="edit_descripcion_{{ $area->id }}"
                                            class="form-control" value="{{ $editDescripcion }}" required>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label for="edit_abreviacion_{{ $area->id }}">Abreviacion</label>
                                        <input type="text" name="abreviacion" id="edit_abreviacion_{{ $area->id }}"
                                            class="form-control" value="{{ $editAbreviacion }}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary"
                                        data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

            <div id="areas-page-data" class="d-none" data-old-modal='@json($oldModal)'
                data-old-edit-area-id='@json($oldEditAreaId)' data-request-modal='@json(request('modal'))'
                data-request-area-id='@json(request('area'))'></div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pageData = document.getElementById('areas-page-data');
            if (!pageData || !window.$) {
                return;
            }

            const parseJSON = (value, fallback = null) => {
                if (!value) {
                    return fallback;
                }

                try {
                    return JSON.parse(value);
                } catch (error) {
                    return fallback;
                }
            };

            const oldModal = parseJSON(pageData.dataset.oldModal);
            const oldEditAreaId = parseJSON(pageData.dataset.oldEditAreaId);
            const requestModal = parseJSON(pageData.dataset.requestModal);
            const requestAreaId = parseJSON(pageData.dataset.requestAreaId);

            const openModal = (selector) => {
                if (!selector || !window.$(selector).length) {
                    return;
                }

                window.$(selector).modal('show');
            };

            if (oldModal === 'create') {
                openModal('#areaCreateModal');
                return;
            }

            if (oldModal === 'edit' && oldEditAreaId) {
                openModal(`#areaEditModal${oldEditAreaId}`);
                return;
            }

            if (requestModal === 'create') {
                openModal('#areaCreateModal');
                return;
            }

            if (requestModal === 'edit' && requestAreaId) {
                openModal(`#areaEditModal${requestAreaId}`);
            }
        });
    </script>
@endsection
