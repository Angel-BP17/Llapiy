@extends('layouts.app')

@section('title', 'Campos adicionales')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Campos adicionales</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Campos adicionales</li>
        </ul>
    </div>
    <!-- Forms Section-->
    <section class="forms">
        <div class="container-fluid">
            <!-- Mostrar mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-md-12">
                    <a class="ml-right btn btn-success" href="{{ route('campos.create') }}"><i
                            class="fa-solid fa-file-invoice"></i> Crear Nuevo Campo</a>
                </div>
            </div>
            <h4>Buscar</h4>
            <form method="GET" action="{{ route('campos.index') }}" class="mb-3">
                <div class="row mt-3">
                    <div class="col-md">
                        <label for="search" class="sr-only">Nombre</label>
                        <input type="text" name="search" id="search" class="form-control"
                            value="{{ request('search') }}" placeholder="Ingrese el nombre">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                    </div>
                    <div class="col-md-auto">
                        <a href="{{ route('document_types.index') }}" class="btn btn-secondary">Limpiar Filtros</a>
                    </div>
                </div>
            </form>
            <div class="m-2 row bg-white has-shadow">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="campoTableBody">
                            @foreach ($campos as $campo)
                                <tr>
                                    <td>{{ $campo->id }}</td>
                                    <td>{{ $campo->name }}</td>
                                    <td>
                                        <a href="{{ route('campos.edit', $campo) }}" class="btn btn-warning btn-sm"><i
                                                class="fa-solid fa-pen"></i> Editar</a>
                                        <form action="{{ route('campos.destroy', $campo) }}" method="POST"
                                            style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar este tipo de campo?')"
                                                @if ($campo->documentTypes()->count() > 0) disabled data-toggle="tooltip" data-placement="top" title="No se puede eliminar porque tiene tipos de documentos asociados" @endif>
                                                <i class="fa-solid fa-trash"></i> Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $campos->links() }}
                </div>
            </div>
        </div>
        <script>
            document.getElementById('campoSearch').addEventListener('keyup', function() {
                let query = this.value.toLowerCase();
                let rows = document.querySelectorAll('#campoTableBody tr');

                rows.forEach(row => {
                    let name = row.children[1].innerText.toLowerCase();
                    row.style.display = name.includes(query) ? '' : 'none';
                });
            });
        </script>
    </section>
@endsection
