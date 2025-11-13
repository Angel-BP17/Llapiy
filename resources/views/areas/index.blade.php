@extends('layouts.app')

@section('title', 'Áreas')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Áreas</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Áreas</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <div class="container-fluid">
            <!-- Mensajes de éxito/error -->
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Botón para crear una nueva área -->
            <div class="mb-3">
                <a href="{{ route('areas.create') }}" class="btn btn-success"><i class="icon-interface-windows"></i> Crear
                    Nueva Área</a>
            </div>

            <!-- Tarjetas para las áreas -->
            <div class="row">
                @forelse ($areas as $area)
                    @if ($area->descripcion !== 'Todas')
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="card-title">{{ $area->descripcion }} ({{ $area->abreviacion }})</h4>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('areas.show', $area->id) }}" class="btn btn-info">
                                        <i class="fa-solid fa-people-group"></i> Ver grupos y subgrupos
                                    </a>
                                </div>
                                <div class="card-footer text-end">
                                    <a href="{{ route('areas.edit', $area->id) }}" class="btn btn-sm btn-warning">Editar
                                        Área</a>
                                    <form action="{{ route('areas.destroy', $area->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Eliminar esta área y sus datos relacionados?')"
                                            @if ($area->groups()->count() > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene grupos asociados" @endif>Eliminar
                                            Área</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="text-center">No se encontraron áreas registradas.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
