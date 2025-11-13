@extends('layouts.app')

@section('title', 'Editar subgrupo')
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
            <li class="breadcrumb-item"><a href="{{ route('areas.index') }}">Áreas</a></li>
            <li class="breadcrumb-item active">Editar subgrupo</li>
        </ul>
    </div>
    <section class="forms">
        <!-- Module content-->
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

                <!-- Formulario para editar subgrupo -->
                <form action="{{ route('subgroups.update', $subgroup->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col">
                            <label for="descripcion" class="form-label">Descripción del Subgrupo</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control"
                                value="{{ $subgroup->descripcion }}" required>
                        </div>
                        <div class="col">
                            <label for="abreviacion" class="form-label">Abreviación</label>
                            <input type="text" name="abreviacion" id="abreviacion" class="form-control"
                                value="{{ $subgroup->abreviacion }}">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('areas.show', $subgroup->group->areaGroupType->area_id) }}"
                                class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
