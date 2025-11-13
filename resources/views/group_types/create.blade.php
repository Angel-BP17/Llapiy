@extends('layouts.app')

@section('title', 'Crear nuevo tipo de grupo')
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
            <li class="breadcrumb-item"><a href="{{ route('group_types.index') }}">Tipos de grupos</a></li>
            <li class="breadcrumb-item active">Crear Nuevo Tipo
                de
                Grupo</li>
        </ul>
    </div>
    <section class="forms">
        <a href="{{ route('group_types.index') }}" class="ml-4 mb-3 btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left-long"></i> Volver
        </a>
        <!-- Module content-->
        <div class="container-fluid">
            <div class="p-5 row bg-white has-shadow">
                <!-- Mostrar errores de validación -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="ml-3" action="{{ route('group_types.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control" required>
                        </div>
                        <div class="col mb-3">
                            <label for="abreviacion" class="form-label">Abreviación</label>
                            <input type="text" name="abreviacion" id="abreviacion" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('group_types.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </section>
@endsection
