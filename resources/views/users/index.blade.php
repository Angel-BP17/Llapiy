@extends('layouts.app')

@section('title', 'Usuarios')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Usuarios</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Usuarios</li>
        </ul>
    </div>

    <section class="forms">
        <!-- Filtros -->
        <div class="container-fluid mb-4">
            <!-- Mostrar mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <!-- Tabla de usuarios -->
            <a class="mb-3 btn btn-success" href="{{ route('users.create') }}">
                <i class="fa-solid fa-user-plus"></i> Crear Usuario
            </a>

            <div class="card bg-white has-shadow mb-4">
                <div class="card-body">
                    <h4>Buscar</h4>
                    <form method="GET" action="{{ route('users.index') }}" class="row">
                        <div class="col-3 mb-3">
                            <label for="search" class="sr-only">Nombre o Apellido</label>
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Nombre o apellido">
                        </div>
                        <div class="col-3 mb-3">
                            <label for="user_type_id" class="sr-only">Tipo de Usuario</label>
                            <select name="user_type_id" id="user_type_id" class="form-control">
                                <option value="">-- Seleccionar Tipo --</option>
                                @foreach ($userTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ request('user_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto mb-3">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i>
                                Aplicar filtros</button>
                        </div>
                        <div class="col-auto mb-3">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fa-solid fa-x"></i>
                                Limpiar
                                filtros</a>
                        </div>

                        @php
                            $pdfUrl = route('users.pdf', request()->all());
                        @endphp

                        <div class="col-auto">
                            <a href="{{ $pdfUrl }}" class="btn btn-danger mb-3 " target="_blank">
                                <i class="fa-solid fa-file-pdf"></i> Generar Reporte
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card m-1 row bg-white has-shadow">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Foto</th>
                                <th>Tipo de usuario</th>
                                <th>Correo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <th scope="row">{{ $user->id }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>
                                        @if ($user->foto_perfil)
                                            <img src="{{ asset('storage/' . $user->foto_perfil) }}" alt="Foto de perfil"
                                                width="35" height="35" class="rounded-circle">
                                        @else
                                            <img src="{{ asset('img/default-avatar.png') }}" alt="Foto de perfil"
                                                width="35" height="35" class="rounded-circle">
                                        @endif
                                    </td>
                                    <td>{{ $user->userType->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm">
                                                <i class="fa-solid fa-eye"></i> Ver
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-pen"></i> Editar
                                            </a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar este usuario? Se borrará permanentemente')">
                                                    <i class="fa-solid fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron usuarios.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $users->links() }}
            </div>

        </div>
    </section>
@endsection
