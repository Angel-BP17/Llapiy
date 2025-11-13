@extends('layouts.app')

@section('title', 'Detalles del documento')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Detalles del documento</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documentos</a></li>
            <li class="breadcrumb-item active">Detalles del documento</li>
        </ul>
    </div>
    <section class="dashboard-counts no-padding-bottom">
        <a href="{{ route('documents.index') }}" class="ml-4 mb-3 btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left-long"></i> Volver
        </a>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <h3 class="mb-0">Información del Documento</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Usuario:</strong>
                                    {{ $document->user->name ?? 'Sin' }} {{ $document->user->last_name ?? 'Usuario' }}</li>
                                <li class="list-group-item"><strong>Número de documento:</strong>
                                    {{ $document->n_documento }}
                                </li>
                                <li class="list-group-item"><strong>Asunto:</strong> {{ $document->asunto }}
                                </li>
                                <li class="list-group-item"><strong>Folios:</strong> {{ $document->folios }}
                                </li>
                                <li class="list-group-item"><strong>Tipo de Documento:</strong>
                                    {{ $document->documentType->name }}</li>
                                @if (Auth::user()->isAdminOrManager())
                                    <li class="list-group-item"><strong>Directorio del archivo:</strong>
                                        {{ $document->root }}</li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Periodo:</strong> {{ $year }}</li>
                                <li class="list-group-item"><strong>Mes:</strong> {{ $month }}</li>
                                <li class="list-group-item"><strong>Día:</strong> {{ $daySem }}
                                    {{ $day }}</li>
                                <li class="list-group-item"><strong>Àrea:</strong>
                                    {{ $document->user->group->areaGroupType->area->descripcion ?? 'Sin área' }}</li>
                                <li class="list-group-item"><strong>Grupo:</strong>
                                    {{ $document->user->group->descripcion ?? 'Sin grupo' }}</li>
                                <li class="list-group-item"><strong>Subgrupo:</strong>
                                    {{ $document->user->subgroup->descripcion ?? 'Sin subgrupo' }}</li>
                            </ul>
                        </div>
                    </div>
                    <!-- Contenedor de campos adicionales -->
                    <div class="row">
                        <div class="col-12">
                            <h4>Campos Adicionales</h4>
                            <div id="campos-container" class="row"></div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="mb-0">Vista Previa del Documento</h3>
                </div>
                <div class="card-body">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe src="{{ asset('storage/' . $document->root) }}" frameborder="0" width="100%"
                            height="600px" allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const campos = @json($campos);
            const camposContainer = document.getElementById("campos-container");
            camposContainer.innerHTML = "";

            if (!campos || campos.length === 0) {
                camposContainer.innerHTML = `
                <div class="col">
                    <p class="text-muted text-center">Este documento no tiene campos adicionales.</p>
                </div>
            `;
                return;
            }

            campos.forEach(campo => {
                const valor = (typeof campo.valor === 'object') ? JSON.stringify(campo.valor) : campo.valor;
                camposContainer.innerHTML += `
                <div class="col-sm-6 mb-3">
                    <label class="form-control-label"><strong>${campo.nombre}:</strong></label>
                    <p class="form-control-static">${valor}</p>
                </div>
            `;
            });
        });
    </script>
@endsection
