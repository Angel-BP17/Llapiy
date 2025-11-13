@extends('layouts.app')

@section('title', 'Andamios')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Andamios</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Secciones</li>
            <li class="breadcrumb-item active">Andamios</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <div class="container-fluid">
            {{-- Mensajes de éxito o error --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <!-- Botón para volver -->
            <a href="{{ route('sections.index') }}" class="mt-4 btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left-long"></i> Volver
            </a>

            <h1 class="mb-3 mt-3">Andamios de la Sección {{ $section->n_section }}</h1>

            {{-- Formulario para agregar un nuevo andamio --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Registrar Nuevo Andamio</div>
                <div class="card-body">
                    <form class="form-inline" action="{{ route('sections.andamios.store', $section->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="n_andamio" class="sr-only">Número de Andamio</label>
                            <input type="number" name="n_andamio" id="n_andamio" class="mr-3 form-control"
                                placeholder="Número de Andamio" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion" class="sr-only">Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" class="mr-3 form-control"
                                placeholder="Descripción" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>
                                Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Listado de andamios en cards --}}
            <div class="row">
                @forelse ($andamios as $andamio)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title">Andamio {{ $andamio->n_andamio }}</h5>
                            </div>
                            <div class="card-body">
                                {{-- Botón para acceder a las cajas --}}
                                <a href="{{ route('sections.andamios.boxes.index', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                                    class="mb-3 btn btn-info btn-sm"><i class="fa-solid fa-arrows-down-to-line"></i>
                                    Ver
                                    Cajas <i class="fa-solid fa-box-open"></i></a>
                                <p class="card-text">Descripción: {{ $andamio->descripcion }}</p>
                            </div>
                            <div class="card-footer">
                                {{-- Botón para editar --}}
                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#editAndamioModal{{ $andamio->id }}"><i class="fa-solid fa-pen"></i>
                                    Editar
                                </button>
                                {{-- Botón para eliminar --}}
                                <form
                                    action="{{ route('sections.andamios.destroy', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                                    method="POST" onsubmit="return confirm('¿Está seguro de eliminar este andamio?')"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        @if ($andamio->boxes->count() > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene cajas asociadas" @endif><i
                                            class="fa-solid fa-trash"></i> Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Modal para editar andamio --}}
                    <div class="modal fade" id="editAndamioModal{{ $andamio->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="editAndamioModalLabel{{ $andamio->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editAndamioModalLabel{{ $andamio->id }}">
                                        Editar
                                        Andamio</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form
                                    action="{{ route('sections.andamios.update', ['section' => $section->id, 'andamio' => $andamio->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="n_andamio">Número de Andamio</label>
                                            <input type="number" name="n_andamio" id="n_andamio" class="form-control"
                                                value="{{ $andamio->n_andamio }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="descripcion">Descripción</label>
                                            <input type="text" name="descripcion" id="descripcion" class="form-control"
                                                value="{{ $andamio->descripcion }}" required>
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
                @empty
                    <div class="col-12">
                        <p class="text-center">No hay andamios registrados en esta sección.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
