@extends('layouts.app')

@section('title', 'Ingresar bloque')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Ingresar bloque</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('blocks.index') }}">Bloques</a></li>
            <li class="breadcrumb-item active">Ingresar bloque</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <form action="{{ route('blocks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <a href="{{ route('blocks.index') }}" class="ml-4 mb-3 btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left-long"></i> Volver
            </a>
            <div class="container-fluid">
                <div class="p-5 bg-white has-shadow">
                    <!-- Mostrar mensajes de error -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-sm form-group">
                            <label for="n_bloque" class="form-control-label">Número de Documento</label>
                            <input type="text" name="n_bloque" id="n_bloque" class="form-control" required>
                        </div>
                        <div class="col-sm form-group">
                            <label for="asunto" class="form-control-label">Asunto</label>
                            <input type="text" class="form-control" id="asunto" name="asunto" required>
                        </div>
                        <div class="col-sm form-group">
                            <label for="root" class="form-control-label">Archivo (.pdf)</label>
                            <input type="file" class="form-control-file" id="root" name="root" accept=".pdf"
                                required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm form-group">
                            <label for="folios" class="form-control-label">Folios</label>
                            <input type="text" class="form-control" id="folios" name="folios">
                        </div>
                        <div class="col-sm form-group">
                            <label for="rango_inicial" class="form-control-label">Rango inicial</label>
                            <input type="number" min="1" step="1" class="form-control" id="rango_inicial"
                                name="rango_inicial" required>
                        </div>
                        <div class="col-sm form-group">
                            <label for="rango_final" class="form-control-label">Rango final</label>
                            <input type="number" min="1" step="1" class="form-control" id="rango_final"
                                name="rango_final" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label for="fecha" class="form-control-label">Fecha</label>
                            <input type="date"class="form-control" id="fecha" name="fecha" required>
                        </div>
                        <div class="col-auto form-group">
                            <button type="submit" class="btn btn-primary" style="margin-top: 30px">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
