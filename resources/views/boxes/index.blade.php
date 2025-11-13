@extends('layouts.app')

@section('title', 'Cajas')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Cajas</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Secciones</li>
            <li class="breadcrumb-item active">Andamios</li>
            <li class="breadcrumb-item active">Cajas</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <div class="container-fluid">
            <!-- Botón para volver -->
            <div class="mb-3 mt-4">
                <a href="{{ route('sections.andamios.index', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                    class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left-long"></i> Volver
                </a>
            </div>
            <h1 class="mt-2 mb-3">Cajas del Andamio {{ $andamio->n_andamio }}</h1>

            {{-- Mensajes de éxito y error --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Formulario para registrar un nueva caja --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Registrar Nueva Caja</div>
                <div class="card-body">
                    <form class="form-inline"
                        action="{{ route('sections.andamios.boxes.store', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                        method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="n_box" class="sr-only">Número de Caja</label>
                            <input type="number" name="n_box" id="n_box" class="mr-3 form-control" required
                                placeholder="Número de Caja">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>
                                Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Listado de cajas --}}
            <div class="row">
                @foreach ($boxes as $box)
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <strong>Caja: {{ $box->n_box }}</strong>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('sections.andamios.boxes.archivos.index', ['section' => $section->id, 'andamio' => $andamio->id, 'box' => $box->id]) }}"
                                    class="btn btn-info btn-sm mb-2"><i class="fa-solid fa-file"></i> Ver
                                    Archivos</a>
                                <p>{{ $box->descripcion }}</p>
                            </div>
                            <div class="card-footer">
                                {{-- Botón para editar caja --}}
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#editBoxModal{{ $box->id }}"><i class="fa-solid fa-pen"></i>
                                    Editar Caja
                                </button>

                                {{-- Botón para eliminar caja --}}
                                <form
                                    action="{{ route('sections.andamios.boxes.destroy', ['section' => $section->id, 'andamio' => $andamio->id, 'box' => $box->id]) }}"
                                    method="POST" style="display: inline-block;"
                                    onsubmit="return confirm('¿Está seguro de eliminar esta caja?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        @if ($box->blocks->count() > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene cajas asociadas" @endif><i
                                            class="fa-solid fa-trash"></i>
                                        Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Modal para editar caja --}}
                    <div class="modal fade" id="editBoxModal{{ $box->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="editBoxModalLabel{{ $box->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editBoxModalLabel{{ $box->id }}">Editar
                                        Caja</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form
                                    action="{{ route('sections.andamios.boxes.update', ['section' => $section->id, 'andamio' => $andamio->id, 'box' => $box->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="n_box">Número de Caja</label>
                                            <input type="number" name="n_box" class="form-control"
                                                value="{{ $box->n_box }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary"><i
                                                class="fa-solid fa-floppy-disk"></i> Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
