@extends('layouts.app')

@section('title', 'Ingresar documento')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Ingresar documento</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documentos</a></li>
            <li class="breadcrumb-item active">Ingresar documento</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row ml-3 mb-4 mr-3">
                <div class="col-4">
                    <label for="document_type" class="form-control-label">Tipo de
                        Documento</label>
                    <select name="document_type_id" id="document_type" class="form-control" required>
                        <option value="">Seleccione un tipo</option>
                        @foreach ($documentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
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
                            <input type="text" name="n_documento" id="n_documento" class="form-control" required>
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
                            <label for="fecha" class="form-control-label">Fecha del documento</label>
                            <input type="date"class="form-control" id="fecha" name="fecha" required>
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
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>

            </div>
        </form>
    </section>
    <script>
        $(document).ready(function() {
            const documentTypes =
                @json($documentTypes);

            let documentTypeSelect = $('#document_type');
            let camposContainer = $('#campos-container');

            console.log(documentTypes);

            function cargarCampos(documentTypeId) {
                camposContainer.html('');
                if (!documentTypeId) return;

                // Encuentra el tipo de documento seleccionado
                let selectedDocumentType = documentTypes.find(type => type.id == documentTypeId);

                // Verificar si el tipo de documento existe
                if (!selectedDocumentType) {
                    camposContainer.append(`
                <div class="col">
                    <p class="text-center text-muted">Tipo de documento no encontrado.</p>
                </div>
            `);
                    return;
                }

                // Verificar si el campoTypes existe y tiene campos
                if (!selectedDocumentType.campo_types || selectedDocumentType.campo_types.length === 0) {

                    console.log(selectedDocumentType);
                    camposContainer.append(`
                <div class="col">
                    <p class="text-center text-muted">Este tipo de documento no tiene campos adicionales.</p>
                </div>
            `);
                    return;
                }

                // Mostrar los campos adicionales
                selectedDocumentType.campo_types.forEach((campo, index) => {
                    let campoHTML = `
                <div class="col-sm-4 form-group mb-3">
                    <label class="form-control-label">${campo.name}</label>
                    <input type="text" name="campos[${index}][dato]" class="form-control" value="">
                    <input type="hidden" name="campos[${index}][id]" value="${campo.id}">
                </div>
            `;
                    camposContainer.append(campoHTML);
                });
            }

            // Se ejecuta cuando se selecciona un tipo de documento
            documentTypeSelect.change(function() {
                let selectedId = $(this).val();
                camposContainer.html(''); // Limpiar los campos al cambiar el tipo de documento

                if (!selectedId) {
                    camposContainer.append(`
                <div class="col">
                    <p class="text-center text-muted">Seleccione un tipo de documento</p>
                </div>
            `);
                    return;
                }
                cargarCampos(selectedId);
            });
        });
    </script>
@endsection
