@extends('layouts.app')

@section('title', 'Registro de actividades')

@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Registro de actividades</h2>
        </div>
    </header>

    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Registro de actividades</li>
        </ul>
    </div>

    <!-- Filtros -->
    <section class="forms">
        <div class="container-fluid">
            <form method="GET" action="{{ route('activity.logs') }}" class="form-inline ml-5 mb-3">
                <!-- Filtro por Fecha -->
                <div class="form-group mx-2 mb-2">
                    <label for="date">Fecha:</label>
                    <input type="date" class="form-control ml-2" id="date" name="date"
                        value="{{ request('date') }}">
                </div>

                <!-- Filtro por Usuario -->
                <div class="form-group mx-2 mb-2">
                    <label for="user_id">Usuario:</label>
                    <select class="form-control ml-2" id="user_id" name="user_id">
                        <option value="">Todos</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Módulo -->
                <div class="form-group mx-2 mb-2">
                    <label for="module">Módulo:</label>
                    <select class="form-control ml-2" id="module" name="module">
                        <option value="">Todos</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                {{ $module }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary ml-2 mb-2">
                    <i class="fa fa-search"></i> Buscar
                </button>
                <a href="{{ route('activity.logs') }}" class="btn btn-secondary ml-2 mb-2">
                    <i class="fa fa-times"></i> Limpiar filtros
                </a>
            </form>
            <!-- Botón para generar PDF con los mismos filtros -->
            @php
                // Construir la URL para PDF con los mismos parámetros de búsqueda
                $pdfUrl = route('activity-logs.pdf', request()->all());
            @endphp

            <a href="{{ $pdfUrl }}" class="btn btn-danger mb-3" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> Generar Reporte
            </a>

            <div class="p-5 bg-white has-shadow">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Módulo</th>
                                <th>Datos Antes</th>
                                <th>Datos Después</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td>{{ $log->user->name ?? 'Desconocido' }} {{ $log->user->last_name ?? '' }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ class_basename($log->model) }}</td>
                                    <td>
                                        @if ($log->before)
                                            <button class="btn btn-sm btn-primary" data-toggle="modal"
                                                data-target="#modalBefore{{ $log->id }}">
                                                Ver Datos
                                            </button>
                                            @include('activity_logs.modal_before', ['log' => $log])
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($log->after)
                                            <button class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#modalAfter{{ $log->id }}">
                                                Ver Datos
                                            </button>
                                            @include('activity_logs.modal_after', ['log' => $log])
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->created_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay actividades registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $logs->links() }} <!-- Paginación -->
            </div>
        </div>
    </section>
@endsection
