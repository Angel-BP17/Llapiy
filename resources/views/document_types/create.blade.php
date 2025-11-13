@extends('layouts.app')

@section('title', 'Crear tipo de documento')
@section('content')
    @vite(['resources/js/documentTypes/document_type_selector.js', 'resources/js/documentTypes/campo_selector.js'])
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Tipos de documentos</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Tipos de documentos</li>
            <li class="breadcrumb-item active">Crear tipo de documento</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <a href="{{ route('document_types.index') }}" class="ml-4 mb-3 btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left-long"></i> Volver
        </a>
        <div class="container-fluid">
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

            <!-- Mostrar mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('document_types.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre del Tipo de Documento</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label>Seleccionar Grupos y Subgrupos</label>
                    <div id="groupTree" class="border p-3 rounded" style="max-height: 400px; overflow-y: auto;"></div>
                </div>

                <h6>Seleccionados:</h6>
                <ul id="selectedItemsList" class="mb-3"></ul>

                <input type="hidden" name="groups" id="selectedGroupsInput">
                <input type="hidden" name="subgroups" id="selectedSubgroupsInput">
                <div class="form-group">
                    <label for="campoSearch">Buscar Campos</label>
                    <input type="text" class="form-control mb-4" id="campoSearch" placeholder="Escriba para buscar...">
                    <div id="campoResults" class="mt-2"></div>
                </div>

                <h5>Campos Seleccionados:</h5>
                <ul id="camposSeleccionados"></ul>

                <input type="hidden" name="campos" id="camposInput">

                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
    </section>
    <script>
        window.campoTypes = @json($campoTypes);
        window.areas = @json($areas);
    </script>
@endsection
