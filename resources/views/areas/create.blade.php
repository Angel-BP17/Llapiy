@extends('layouts.app')

@section('title', 'Crear Área')
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
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Áreas</a></li>
            <li class="breadcrumb-item active">Crear Área</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <div class="container-fluid">
            <div class="p-5 bg-white has-shadow">
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

                <!-- Formulario para crear área -->
                <form action="{{ route('areas.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción del Área</label>
                        <input type="text" name="descripcion" id="descripcion" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="abreviacion" class="form-label">Abreviación</label>
                        <input type="text" name="abreviacion" id="abreviacion" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('areas.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </section>
@endsection
