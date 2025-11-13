@extends('layouts.app')

@section('title', 'Editar documento')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Editar documento</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documentos</a></li>
            <li class="breadcrumb-item active">Editar documento</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row ml-3 mb-4 mr-3">
                <div class="col-4">
                    <label for="documents_type_id" class="form-control-label">Tipo de
                        Documento</label>
                    <select id="document_type_id" name="document_type_id" class="form-control" disabled>
                        <option value="">Seleccione un tipo</option>
                        @foreach ($documentTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ $document->document_type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <a href="{{ route('documents.index') }}" class="ml-4 mb-3 btn btn-secondary btn-sm">
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

                    <h5 class="mb-3">Campos generales</h5>

                    <div class="row">
                        <div class="col-sm form-group">
                            <label for="n_documento" class="form-control-label">Número de Documento</label>
                            <input type="text" name="n_documento" id="n_documento" class="form-control"
                                value="{{ $document->n_documento }}" required>
                        </div>
                        <div class="col-sm form-group">
                            <label for="asunto" class="form-control-label">Asunto</label>
                            <input type="text" class="form-control" id="asunto" name="asunto"
                                value="{{ $document->asunto }}" required>
                        </div>
                        <div class="col-sm form-group">
                            <label for="root" class="form-control-label">Archivo Actual</label>
                            <div>
                                <a href="{{ asset('storage/' . $document->root) }}" target="_blank"
                                    class="btn btn-warning">Ver Archivo Actual</a>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm form-group">
                            <label for="root" class="form-control-label">Nuevo Archivo (.pdf)</label>
                            <input type="file" class="form-control-file" id="root" name="root" accept=".pdf">
                            <small class="form-text text-muted">Deja este campo vacío si no deseas reemplazar el
                                archivo actual.</small>
                        </div>
                        <div class="col-sm form-group">
                            <label for="folios" class="form-control-label">Folios</label>
                            <input type="text" class="form-control" id="folios" name="folios"
                                value="{{ $document->folios }}">
                        </div>
                        <div class="col-sm form-group">
                            <label for="fecha" class="form-control-label">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control"
                                value="{{ $document->fecha->format('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>

                <div class="mt-5 p-5 bg-white has-shadow">
                    <h5 class="mb-3">Campos adicionales</h5>
                    <div class="row" id="campos-container">
                        <!-- Los campos adicionales aparecerán aquí dinámicamente -->
                        <div class="col">
                            <p class="text-center">Seleccione un tipo de documento</p>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </form>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Los datos de documentTypes y camposExistentes se pasan desde el backend
                const documentTypes = @json($documentTypes); // Los datos de documentTypes
                const camposExistentes = @json($document->campos); // Los campos existentes del documento

                let selectDocumentType = document.getElementById("document_type_id");
                let camposContainer = document.getElementById("campos-container");

                // Función para cargar los campos adicionales
                function cargarCampos(documentTypeId) {
                    camposContainer.innerHTML = ''; // Limpiar los campos al cargar

                    // Buscar el tipo de documento seleccionado
                    let selectedDocumentType = documentTypes.find(type => type.id == documentTypeId);

                    // Verificar si el tipo de documento tiene campos adicionales
                    if (!selectedDocumentType || !selectedDocumentType.campo_types || selectedDocumentType.campo_types
                        .length === 0) {
                        camposContainer.innerHTML = `
                <div class="col">
                    <p class="text-center text-muted">Este tipo de documento no tiene campos adicionales.</p>
                </div>
            `;
                        return;
                    }

                    // Mostrar los campos adicionales
                    selectedDocumentType.campo_types.forEach((campoType, index) => {
                        // Buscar si el campo ya tiene un valor asignado
                        const campoExistente = camposExistentes.find(c => c.campo_type_id == campoType.id);
                        let valor = campoExistente ? campoExistente.dato : ''; // Si existe el valor, usarlo

                        // Crear los campos adicionales en el formulario
                        let campoHTML = `
                <div class="col-sm-4 form-group mb-3">
                    <label class="form-control-label">${campoType.name}</label>
                    <input type="text" name="campos[${index}][dato]" class="form-control" value="${valor}">
                    <input type="hidden" name="campos[${index}][id]" value="${campoType.id}">
                </div>
            `;
                        camposContainer.innerHTML += campoHTML;
                    });
                }

                // Ejecutar al cargar la página si hay un tipo de documento seleccionado
                if (selectDocumentType.value) {
                    cargarCampos(selectDocumentType.value);
                }

                // Ejecutar cuando el usuario cambia la selección de tipo de documento
                selectDocumentType.addEventListener("change", function() {
                    let selectedId = this.value;
                    camposContainer.innerHTML = ''; // Limpiar los campos al cambiar

                    if (!selectedId) {
                        camposContainer.innerHTML = `
                <div class="col">
                    <p class="text-center text-muted">Seleccione un tipo de documento</p>
                </div>
            `;
                        return;
                    }

                    cargarCampos(selectedId);
                });
            });
        </script>
    </section>
@endsection
