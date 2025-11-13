@extends('layouts.app')

@section('title', 'Secciones')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Secciones</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Secciones</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Registrar Nueva Sección</h4>
                </div>
                <div class="card-body">
                    <form class="form-inline" method="POST" action="{{ route('sections.store') }}">

                        @csrf
                        <div class="form-group">
                            <label for="n_section" class="sr-only">Número de Sección</label>
                            <input type="number" name="n_section" class="mr-3 form-control" min="1" step="1"
                                placeholder="Número de Sección" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion" class="sr-only">Descripción</label>
                            <input type="text" name="descripcion" class="form-control" placeholder="Descripción"
                                required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="ml-3 btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>
                                Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row mt-4">
                @foreach ($sections as $section)
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title">Sección {{ $section->n_section }}</h5>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('sections.andamios.index', ['section' => $section->id]) }}"
                                    class="btn btn-info btn-sm mb-2"><i class="fa-solid fa-grip-vertical"></i> Ver
                                    Andamios</a>
                                <p class="card-text">Descripción: {{ $section->descripcion }}</p>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#editModal{{ $section->id }}"><i class="fa-solid fa-pen"></i>
                                    Editar</button>
                                <form method="POST" action="{{ route('sections.destroy', ['section' => $section->id]) }}"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Está seguro de eliminar este andamio?')"
                                        @if ($section->andamios->count() > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene andamios asociados" @endif><i
                                            class="fa-solid fa-trash"></i> Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @foreach ($sections as $section)
            <!-- Modal Editar Sección -->
            <div class="modal fade" id="editModal{{ $section->id }}" tabindex="-1" role="dialog"
                aria-labelledby="editModalLabel{{ $section->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $section->id }}">Editar Sección</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('sections.update', ['section' => $section->id]) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="n_section">Número de Sección</label>
                                    <input type="number" name="n_section" class="form-control"
                                        value="{{ $section->n_section }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <input type="text" name="descripcion" class="form-control"
                                        value="{{ $section->descripcion }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>
                                    Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </section>
@endsection
