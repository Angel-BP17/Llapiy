@extends('layouts.app')

@section('title', 'Campos adicionales')
@section('content')
    @vite('resources/js/campos/index.js')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Campos adicionales</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Campos adicionales</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms module-ui">
        <div class="container-fluid">
            <!-- Mostrar mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="card module-hero has-shadow mb-4">
                <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <div class="module-hero-chip mb-2">
                            <i class="fa-solid fa-list-check"></i> Definición de metadatos
                        </div>
                        <h4 class="mb-1 text-white">Campos adicionales</h4>
                        <p class="mb-0 module-hero-text text-white-50">Configura campos reutilizables para enriquecer la
                            información documental.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $campos->total() }} registros
                        </span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-blue"><i class="fa-solid fa-file-lines"></i></div>
                            <div>
                                <div class="text-muted small">Total de campos</div>
                                <div class="h4 mb-0">{{ $totalCampos }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <button class="btn btn-success" type="button" data-toggle="modal" data-target="#campoCreateModal">
                        <i class="fa-solid fa-file-invoice"></i> Crear Nuevo Campo
                    </button>
                </div>
            </div>
            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body">
                    <h4>Buscar</h4>
                    <form method="GET" action="{{ route('campos.index') }}" class="mb-0">
                        <div class="row mt-3 align-items-end">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="search" class="sr-only">Nombre</label>
                                <input type="text" name="search" id="search" class="form-control"
                                    value="{{ request('search') }}" placeholder="Ingrese el nombre">
                            </div>
                            <div class="col-12 col-md-auto mb-3">
                                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                            </div>
                            <div class="col-12 col-md-auto mb-3">
                                <a href="{{ route('campos.index') }}" class="btn btn-secondary">Limpiar Filtros</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card module-table-card bg-white has-shadow mb-4">
                <div class="table-responsive">
                    <table class="table module-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Tipo de dato</th>
                                <th>Configuracion</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="campoTableBody">
                            @forelse ($campos as $index => $campo)
                                <tr>
                                    <td>{{ $campos->firstItem() + $index }}</td>
                                    <td>{{ $campo->name }}</td>
                                    <td>
                                        <span
                                            class="badge badge-info text-uppercase">{{ $campo->data_type ?? 'string' }}</span>
                                    </td>
                                    <td>
                                        <div class="small text-muted">
                                            <span class="d-inline-block mr-2">
                                                {{ $campo->is_nullable ? 'Nullable' : 'No nullable' }}
                                            </span>
                                            @if (!is_null($campo->length))
                                                <span class="d-inline-block mr-2">Longitud: {{ $campo->length }}</span>
                                            @endif
                                            @if (in_array($campo->data_type, ['int', 'float', 'double'], true))
                                                <span class="d-inline-block mr-2">
                                                    {{ $campo->allow_negative ? 'Permite negativos' : 'Sin negativos' }}
                                                </span>
                                                <span class="d-inline-block">
                                                    {{ $campo->allow_zero ? 'Permite cero' : 'Sin cero' }}
                                                </span>
                                            @endif
                                            @if ($campo->data_type === 'enum' && !empty($campo->enum_values))
                                                <div class="mt-1">
                                                    <strong>Valores:</strong> {{ implode(', ', $campo->enum_values) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $campoData = [
                                                'id' => $campo->id,
                                                'name' => $campo->name,
                                                'data_type' => $campo->data_type,
                                                'is_nullable' => $campo->is_nullable,
                                                'length' => $campo->length,
                                                'allow_negative' => $campo->allow_negative,
                                                'allow_zero' => $campo->allow_zero,
                                                'enum_values' => $campo->enum_values,
                                            ];
                                        @endphp
                                        <button type="button" class="btn btn-warning btn-sm js-campo-edit"
                                            data-toggle="modal" data-target="#campoEditModal"
                                            data-campo='@json($campoData)'>
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </button>
                                        <form action="{{ route('campos.destroy', $campo) }}" method="POST"
                                            style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar este tipo de campo?')"
                                                @if ($campo->document_types_count > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene tipos de documentos asociados" @endif>
                                                <i class="fa-solid fa-trash"></i> Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No se encontraron campos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $campos->links() }}
                </div>
            </div>
        </div>

        <!-- Modales -->
        <div class="modal fade" id="campoCreateModal" tabindex="-1" role="dialog" aria-labelledby="campoCreateLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="campoCreateLabel">Crear nuevo campo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('campos.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_modal" value="create">
                        <div class="modal-body">
                            @if ($errors->any() && old('_modal') === 'create')
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="campo_create_name" class="form-label">Nombre del Tipo de Campo</label>
                                <input type="text" class="form-control" id="campo_create_name" name="name"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="campo_create_data_type" class="form-label">Tipo de dato</label>
                                <select class="form-control" id="campo_create_data_type" name="data_type" required>
                                    @foreach ($dataTypes as $dataType)
                                        <option value="{{ $dataType }}" @selected(old('data_type', 'string') === $dataType)>
                                            {{ strtoupper($dataType) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="campo_create_length" class="form-label">Longitud (opcional)</label>
                                <input type="number" min="1" class="form-control" id="campo_create_length"
                                    name="length" value="{{ old('length') }}">
                            </div>
                            <div class="form-check mb-3">
                                <input type="hidden" name="is_nullable" value="0">
                                <input class="form-check-input" type="checkbox" value="1"
                                    id="campo_create_is_nullable" name="is_nullable" @checked(old('is_nullable', '1') == '1')>
                                <label class="form-check-label" for="campo_create_is_nullable">
                                    Permitir valor nulo
                                </label>
                            </div>
                            <div id="campo_create_numeric_options" class="border rounded p-3 mb-3 d-none">
                                <p class="mb-2 small text-muted">Opciones para INT/FLOAT/DOUBLE</p>
                                <div class="form-check mb-2">
                                    <input type="hidden" name="allow_negative" value="0">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="campo_create_allow_negative" name="allow_negative"
                                        @checked(old('allow_negative') == '1')>
                                    <label class="form-check-label" for="campo_create_allow_negative">
                                        Permitir valores negativos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="hidden" name="allow_zero" value="0">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="campo_create_allow_zero" name="allow_zero" @checked(old('allow_zero', '1') == '1')>
                                    <label class="form-check-label" for="campo_create_allow_zero">
                                        Permitir valor 0
                                    </label>
                                </div>
                            </div>
                            <div id="campo_create_enum_options" class="border rounded p-3 mb-3 d-none">
                                <label for="campo_create_enum_values" class="form-label mb-1">Valores del enum</label>
                                <textarea class="form-control" id="campo_create_enum_values" name="enum_values" rows="3"
                                    placeholder="Ej: Pendiente, Aprobado, Rechazado">{{ old('enum_values') }}</textarea>
                                <small class="text-muted">Separe cada opcion por coma o salto de linea.</small>
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

        <div class="modal fade" id="campoEditModal" tabindex="-1" role="dialog" aria-labelledby="campoEditLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="campoEditLabel">Editar campo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="campoEditForm" action="{{ route('campos.update', ['campo' => 'CAMPO_ID']) }}"
                        data-action-template="{{ route('campos.update', ['campo' => 'CAMPO_ID']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_modal" value="edit">
                        <input type="hidden" name="edit_campo_id" id="edit_campo_id"
                            value="{{ old('edit_campo_id') }}">
                        <div class="modal-body">
                            @if ($errors->any() && old('_modal') === 'edit')
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="campo_edit_name" class="form-label">Nombre del Tipo de Campo</label>
                                <input type="text" class="form-control" id="campo_edit_name" name="name"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="campo_edit_data_type" class="form-label">Tipo de dato</label>
                                <select class="form-control" id="campo_edit_data_type" name="data_type" required>
                                    @foreach ($dataTypes as $dataType)
                                        <option value="{{ $dataType }}" @selected(old('data_type', 'string') === $dataType)>
                                            {{ strtoupper($dataType) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="campo_edit_length" class="form-label">Longitud (opcional)</label>
                                <input type="number" min="1" class="form-control" id="campo_edit_length"
                                    name="length" value="{{ old('length') }}">
                            </div>
                            <div class="form-check mb-3">
                                <input type="hidden" name="is_nullable" value="0">
                                <input class="form-check-input" type="checkbox" value="1"
                                    id="campo_edit_is_nullable" name="is_nullable" @checked(old('is_nullable', '1') == '1')>
                                <label class="form-check-label" for="campo_edit_is_nullable">
                                    Permitir valor nulo
                                </label>
                            </div>
                            <div id="campo_edit_numeric_options" class="border rounded p-3 mb-3 d-none">
                                <p class="mb-2 small text-muted">Opciones para INT/FLOAT/DOUBLE</p>
                                <div class="form-check mb-2">
                                    <input type="hidden" name="allow_negative" value="0">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="campo_edit_allow_negative" name="allow_negative" @checked(old('allow_negative') == '1')>
                                    <label class="form-check-label" for="campo_edit_allow_negative">
                                        Permitir valores negativos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="hidden" name="allow_zero" value="0">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="campo_edit_allow_zero" name="allow_zero" @checked(old('allow_zero', '1') == '1')>
                                    <label class="form-check-label" for="campo_edit_allow_zero">
                                        Permitir valor 0
                                    </label>
                                </div>
                            </div>
                            <div id="campo_edit_enum_options" class="border rounded p-3 mb-3 d-none">
                                <label for="campo_edit_enum_values" class="form-label mb-1">Valores del enum</label>
                                <textarea class="form-control" id="campo_edit_enum_values" name="enum_values" rows="3"
                                    placeholder="Ej: Pendiente, Aprobado, Rechazado">{{ old('enum_values') }}</textarea>
                                <small class="text-muted">Separe cada opcion por coma o salto de linea.</small>
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

        <div id="campos-page-data" class="d-none" data-old-modal='@json(old('_modal'))'
            data-old-edit-id='@json(old('edit_campo_id'))' data-old-edit-name='@json(old('name'))'
            data-old-edit-data-type='@json(old('data_type', 'string'))' data-old-edit-length='@json(old('length'))'
            data-old-edit-is-nullable='@json(old('is_nullable', '1'))'
            data-old-edit-allow-negative='@json(old('allow_negative', '0'))'
            data-old-edit-allow-zero='@json(old('allow_zero', '1'))'
            data-old-edit-enum-values='@json(old('enum_values'))'></div>
    </section>
@endsection
