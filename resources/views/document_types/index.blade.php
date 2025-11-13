@extends('layouts.app')

@section('title', 'Tipos de documentos')
@section('content')
    @vite(['resources/js/area_group_subgroup_selector.js'])
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Tipos de documentos</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Tipos de documentos</li>
        </ul>
    </div>
    <!-- Forms Section-->
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
                    <a class="ml-right btn btn-success" href="{{ route('document_types.create') }}"><i
                            class="fa-solid fa-file-invoice"></i> Crear Nuevo Tipo de Documento</a>
                </div>
            </div>
            <h4>Buscar</h4>
            <form method="GET" action="{{ route('document_types.index') }}" class="mb-3">
                <div class="row mt-3">
                    <div class="col-md-3 mb-3">
                        <label for="name" class="sr-only">Nombre</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="{{ request('search') }}" placeholder="Ingrese el nombre">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="area_id" class="sr-only">Área</label>
                        <select name="area_id" id="area_id" class="form-control">
                            <option value="">-- Seleccionar Área --</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->descripcion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
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
                    <div class="col-md-3 mb-3">
                        <label for="subgroup_id" class="sr-only">Subgrupo</label>
                        <select name="subgroup_id" id="subgroup_id" class="form-control">
                            <option value="">-- Seleccionar Subgrupo</option>
                            @foreach ($subgroups as $subgroup)
                                {{ $subgroup->descripcion }}
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto mb-3">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Aplicar
                            Filtros</button>
                    </div>
                    <div class="col-md-auto mb-3">
                        <a href="{{ route('document_types.index') }}" class="btn btn-secondary"><i
                                class="fa-solid fa-x"></i> Limpiar Filtros</a>
                    </div>
                </div>
            </form>

            <div class="m-2 row bg-white has-shadow">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Campos</th>
                                <th>Grupos</th>
                                <th>Subgrupos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documentTypes as $index => $documentType)
                                <tr>
                                    <td>{{ $documentTypes->firstItem() + $index }}</td>
                                    <td>{{ $documentType->name }}</td>
                                    <td>
                                        {{-- Botón para mostrar campos en un modal --}}
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#modalCampos{{ $documentType->id }}">
                                            <i class="fa-solid fa-eye mr-1"></i>Ver
                                        </button>

                                        {{-- Modal de campos --}}
                                        <div class="modal fade" id="modalCampos{{ $documentType->id }}" tabindex="-1"
                                            role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Campos de "{{ $documentType->name }}"</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <ul>
                                                            @foreach ($documentType->campoTypes as $campoType)
                                                                <li>{{ $campoType->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#modalGrupos{{ $documentType->id }}">
                                            <i class="fa-solid fa-eye mr-1"></i>Ver
                                        </button>

                                        <div class="modal fade" id="modalGrupos{{ $documentType->id }}" tabindex="-1"
                                            role="dailog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-tittle">Grupos de "{{ $documentType->name }}"</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <ul>
                                                            @if (!$documentType->groups->isEmpty())
                                                                @foreach ($documentType->groups as $group)
                                                                    <li>{{ $group->descripcion }}</li>
                                                                @endforeach
                                                            @else
                                                                <p>Este tipo de documento no está relacionado a ningún
                                                                    grupo</p>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#modalSubgrupos{{ $documentType->id }}">
                                            <i class="fa-solid fa-eye mr-1"></i>Ver
                                        </button>

                                        <div class="modal fade" id="modalSubgrupos{{ $documentType->id }}"
                                            tabindex="-1" role="dailog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-tittle">Subgrupos de "{{ $documentType->name }}"
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <ul>
                                                            @if (!$documentType->subgroups->isEmpty())
                                                                @foreach ($documentType->subgroups as $subgroup)
                                                                    <li>{{ $subgroup->descripcion }}</li>
                                                                @endforeach
                                                            @else
                                                                <p>Este tipo de documento no está relacionado a ningún
                                                                    subgrupo</p>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('document_types.edit', $documentType) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-pen"></i> Editar
                                            </a>
                                            <form action="{{ route('document_types.destroy', $documentType) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar este tipo de documento? Se borrará permanentemente')"
                                                    @if ($documentType->documents()->count() > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene documentos asociados" @endif>
                                                    <i class="fa-solid fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron tipos de documentos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $documentTypes->links() }}
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
