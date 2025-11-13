@extends('layouts.app')

@section('title', 'Archivos')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Archivos</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Almacén</li>
            <li class="breadcrumb-item active">Secciones</li>
            <li class="breadcrumb-item active">Andamios</li>
            <li class="breadcrumb-item active">Cajas</li>
            <li class="breadcrumb-item active">Archivos</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <div class="container-fluid">
            <!-- Mensajes de éxito -->
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <!-- Botón para volver -->
            <div class="mb-3 mt-4">
                <a href="{{ route('sections.andamios.boxes.index', ['section' => $section, 'andamio' => $andamio, 'box' => $box]) }}"
                    class="btn btn-secondary"><i class="fa-solid fa-arrow-left-long"></i> Volver</a>
            </div>
            <h1 class="mb-4 mt-4">Archivos en la caja {{ $box->n_box }}</h1>
            <div class="row p-4 bg-white has-shadow">

                <!-- Tabla de archivos -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Asunto</th>
                                <th>Folios</th>
                                <th>Periodo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($box->blocks as $block)
                                <tr>
                                    <td>{{ $block->id }}</td>
                                    <td>{{ $block->asunto }}</td>
                                    <td>{{ $block->folios }}</td>
                                    <td>{{ $block->periodo }}</td>
                                    <td>
                                        <a href="{{ route('blocks.show', $block) }}" class="btn btn-info btn-sm"><i
                                                class="fa-solid fa-eye"></i> Ver
                                            Documento</a>
                                        <form style="display: inline-block;"
                                            action="{{ route('sections.andamios.boxes.archivos.move', [
                                                'section' => $section,
                                                'andamio' => $andamio,
                                                'box' => $box,
                                                'block' => $block,
                                            ]) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Está seguro de retirar este archivo?')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm"><i
                                                    class="fa-solid fa-x"></i>
                                                Retirar del almacen</button>
                                        </form>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay archivos en este paquete.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
