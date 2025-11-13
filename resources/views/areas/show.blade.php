@extends('layouts.app')

@section('title', 'Detalles del área')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Deralles del área</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('areas.index') }}">Áreas</a></li>
            <li class="breadcrumb-item active">Detalles del área de {{ $area->descripcion }}</li>
        </ul>
    </div>
    <section class="forms">
        <!-- Module content-->
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <!-- Formulario para agregar un grupo -->
            <div class="mb-4">
                <a href="{{ route('areas.index') }}" class="btn btn-secondary mb-3"><i
                        class="fa-solid fa-arrow-left-long"></i> Volver</a>
                <div class="mb-3">
                    <h3>Agregar Grupo</h3>
                </div>
                <div class="p-2 bg-white has-shadow">
                    <form class="form-inline m-3" action="{{ route('groups.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="area_id" value="{{ $area->id }}">
                        <div class="form-group">
                            <label for="descripcion" class="sr-only">Descripcion</label>
                            <input type="text" name="descripcion" class="form-control mr-2"
                                placeholder="Descipción del grupo">
                        </div>
                        <div class="form-group">
                            <label for="group_type_id" class="sr-only">Tipo de Grupo</label>
                            <select name="group_type_id" id="group_type_id"
                                class="form-control @error('group_type_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Seleccione un tipo</option>
                                @foreach ($groupTypes as $gt)
                                    <option value="{{ $gt->id }}">
                                        {{ $gt->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary ml-3"><i class="fa-solid fa-floppy-disk"></i>
                                Guardar
                                Grupo</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mostrar Grupos agrupados por Tipo -->
            @php
                $groupedByType = $area->groups->groupBy(function ($group) {
                    return $group->areaGroupType->groupType->descripcion ?? 'Sin Tipo';
                });
            @endphp

            @foreach ($groupedByType as $groupType => $groups)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title">{{ $groupType }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($groups as $group)
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-secondary text-white">
                                            <h5 class="card-title">{{ $group->descripcion }}
                                                ({{ $group->abreviacion }})
                                            </h5>
                                        </div>
                                        <div class="card-body">

                                            <!-- Formulario para agregar un subgrupo -->
                                            <h6>Agregar Subgrupo</h6>
                                            <form class="form-horizontal" action="{{ route('subgroups.store') }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="group_id" value="{{ $group->id }}">
                                                <div class="form-row">
                                                    <div class="col form-group">
                                                        <label for="descripcion_subgrupo_{{ $group->id }}"
                                                            class="sr-only">Descripción del Subgrupo</label>
                                                        <input type="text" name="descripcion"
                                                            id="descripcion_subgrupo_{{ $group->id }}"
                                                            class="mr-1 form-control" placeholder="Descripción del Subgrupo"
                                                            required>
                                                    </div>
                                                    <div class="col form-group">
                                                        <label for="abreviacion_subgrupo_{{ $group->id }}"
                                                            class="sr-only">Abreviación</label>
                                                        <input type="text" name="abreviacion"
                                                            id="abreviacion_subgrupo_{{ $group->id }}"
                                                            class="mr-2 form-control" placeholder="Abreviación" required>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary"><i
                                                        class="fa-solid fa-floppy-disk"></i> Guardar
                                                    Subgrupo</button>
                                            </form>

                                            <div class="mt-3 mb-2">
                                                <h6>Subgrupos</h6>
                                            </div>
                                            @php
                                                $rootSubgroups = $group->subgroups->whereNull('parent_subgroup_id');
                                            @endphp

                                            @if ($rootSubgroups->isNotEmpty())
                                                @include('subgroups.tree', ['subgroups' => $rootSubgroups])
                                            @else
                                                <p class="text-muted"><em>No hay subgrupos registrados.</em></p>
                                            @endif
                                        </div>
                                        <div class="card-footer text-end">
                                            <a href="{{ route('groups.edit', $group->id) }}"
                                                class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i>
                                                Editar Grupo</a>
                                            <form action="{{ route('groups.destroy', $group->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('¿Eliminar este grupo?')"
                                                    @if ($group->subgroups()->count() > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene subgrupos asociados" @endif><i
                                                        class="fa-solid fa-trash"></i>
                                                    Eliminar
                                                    Grupo</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
