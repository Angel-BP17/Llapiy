@extends('layouts.app')

@section('title', 'Tipos de grupos')
@section('content')
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Tipos de grupos</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Tipos de grupos</li>
        </ul>
    </div>

    <section class="forms module-ui">
        <div class="container-fluid">
            @php
                $oldModal = old('_modal');
                $oldEditGroupTypeId = old('edit_group_type_id');
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
                            <i class="fa-solid fa-sitemap"></i> Catalogo institucional
                        </div>
                        <h4 class="mb-1 text-white">Gestion de tipos de grupos</h4>
                        <p class="mb-0 module-hero-text text-white-50">Define y organiza categorias para la estructura de
                            areas y grupos.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $groupTypes->total() }} registros
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-4 align-items-center">
                <div class="col-md-4 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-violet"><i class="fa-solid fa-sitemap"></i></div>
                            <div>
                                <div class="text-muted small">Tipos de grupos</div>
                                <div class="h4 mb-0">{{ $groupTypes->total() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 mb-3 text-md-right">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#groupTypeCreateModal">
                        Crear nuevo tipo de grupo
                    </button>
                </div>
            </div>

            <div class="card module-table-card bg-white has-shadow mb-4">
                <div class="table-responsive">
                    <table class="table module-table">
                        <thead>
                            <tr>
                                <th>Descripcion</th>
                                <th>Abreviacion</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($groupTypes as $groupType)
                                <tr>
                                    <td>{{ $groupType->descripcion }}</td>
                                    <td>{{ $groupType->abreviacion }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                            data-target="#groupTypeEditModal{{ $groupType->id }}">
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </button>
                                        <form action="{{ route('group_types.destroy', $groupType->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Estas seguro de eliminar este tipo de grupo?')"
                                                @if (!$groupType->canBeDeleted()) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene documentos asociados" @endif>
                                                <i class="fa-solid fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay tipos de grupo registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $groupTypes->links() }}
                </div>
            </div>

            <div class="modal fade" id="groupTypeCreateModal" tabindex="-1" role="dialog"
                aria-labelledby="groupTypeCreateLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="groupTypeCreateLabel">Crear tipo de grupo</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('group_types.store') }}" method="POST">
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
                                    <label for="create_descripcion">Descripcion</label>
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
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @foreach ($groupTypes as $groupType)
                @php
                    $isCurrentEditError = $oldModal === 'edit' && (int) $oldEditGroupTypeId === $groupType->id;
                    $editDescripcion = $isCurrentEditError ? old('descripcion') : $groupType->descripcion;
                    $editAbreviacion = $isCurrentEditError ? old('abreviacion') : $groupType->abreviacion;
                @endphp
                <div class="modal fade" id="groupTypeEditModal{{ $groupType->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="groupTypeEditLabel{{ $groupType->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="groupTypeEditLabel{{ $groupType->id }}">Editar tipo de grupo
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('group_types.update', $groupType->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_modal" value="edit">
                                <input type="hidden" name="edit_group_type_id" value="{{ $groupType->id }}">
                                <div class="modal-body">
                                    @if ($errors->any() && $isCurrentEditError)
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
                                        <label for="edit_descripcion_{{ $groupType->id }}">Descripcion</label>
                                        <input type="text" name="descripcion"
                                            id="edit_descripcion_{{ $groupType->id }}" class="form-control"
                                            value="{{ $editDescripcion }}" required>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label for="edit_abreviacion_{{ $groupType->id }}">Abreviacion</label>
                                        <input type="text" name="abreviacion"
                                            id="edit_abreviacion_{{ $groupType->id }}" class="form-control"
                                            value="{{ $editAbreviacion }}">
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

            <div id="group-types-page-data" class="d-none" data-old-modal='@json($oldModal)'
                data-old-edit-id='@json($oldEditGroupTypeId)' data-request-modal='@json(request('modal'))'
                data-request-id='@json(request('group_type'))'></div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pageData = document.getElementById('group-types-page-data');
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
                    try {
                        const textArea = document.createElement('textarea');
                        textArea.innerHTML = value;
                        return JSON.parse(textArea.value);
                    } catch {
                        return fallback;
                    }
                }
            };

            const oldModal = parseJSON(pageData.dataset.oldModal);
            const oldEditId = parseJSON(pageData.dataset.oldEditId);
            const requestModal = parseJSON(pageData.dataset.requestModal);
            const requestId = parseJSON(pageData.dataset.requestId);

            const openModal = (selector) => {
                if (!selector || !window.$(selector).length) {
                    return;
                }

                window.$(selector).modal('show');
            };

            if (oldModal === 'create') {
                openModal('#groupTypeCreateModal');
                return;
            }

            if (oldModal === 'edit' && oldEditId) {
                openModal(`#groupTypeEditModal${oldEditId}`);
                return;
            }

            if (requestModal === 'create') {
                openModal('#groupTypeCreateModal');
                return;
            }

            if (requestModal === 'edit' && requestId) {
                openModal(`#groupTypeEditModal${requestId}`);
            }
        });
    </script>
@endsection
