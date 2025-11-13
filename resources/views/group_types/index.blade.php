@extends('layouts.app')

@section('title', 'Tipos de grupos')
@section('content')
    <!-- Page Header-->
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
    <section class="forms">
        <!-- Module content-->
        <div class="container-fluid">
            <!-- Botón para crear un nuevo tipo de grupo -->
            <a href="{{ route('group_types.create') }}" class="mb-3 btn btn-primary">Crear Nuevo Tipo
                de
                Grupo</a>
            <div class="m-2 row bg-white has-shadow">
                <!-- Mensajes de éxito/error -->
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="table-responsive">
                    <!-- Tabla de tipos de grupo -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Abreviación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($groupTypes as $groupType)
                                <tr>
                                    <td>{{ $groupType->descripcion }}</td>
                                    <td>{{ $groupType->abreviacion }}</td>
                                    <td>
                                        <a href="{{ route('group_types.edit', $groupType->id) }}"
                                            class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i> Editar</a>
                                        <form action="{{ route('group_types.destroy', $groupType->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Estás seguro de eliminar este tipo de grupo?')"
                                                @if (!$groupType->canBeDeleted()) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene documentos asociados" @endif><i
                                                    class="fa-solid fa-trash"></i> Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay tipos de grupo registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
