@extends('layouts.app')

@section('title', 'Registro de actividades')

@section('content')
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

    <section class="forms module-ui">
        <div class="container-fluid">
            <div class="card module-hero has-shadow mb-4">
                <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <div class="module-hero-chip mb-2">
                            <i class="fa-solid fa-clock-rotate-left"></i> Auditoría del sistema
                        </div>
                        <h4 class="mb-1 text-white">Registro de actividades</h4>
                        <p class="mb-0 module-hero-text text-white-50">Revisa cambios, usuarios y modulos afectados para
                            seguimiento operativo.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $logs->total() }} registros
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-blue"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            <div>
                                <div class="text-muted small">Actividades listadas</div>
                                <div class="h4 mb-0">{{ $logs->total() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-violet"><i class="fa-solid fa-users"></i></div>
                            <div>
                                <div class="text-muted small">Usuarios en filtro</div>
                                <div class="h4 mb-0">{{ $users->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-green"><i class="fa-solid fa-cubes"></i></div>
                            <div>
                                <div class="text-muted small">Módulos detectados</div>
                                <div class="h4 mb-0">{{ count($modules) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('activity.logs') }}">
                        <div class="row align-items-end">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="date">Fecha</label>
                                <input type="date" class="form-control" id="date" name="date"
                                    value="{{ request('date') }}">
                            </div>

                            <div class="col-12 col-md-3 mb-3">
                                <label for="user_id">Usuario</label>
                                <select class="form-control" id="user_id" name="user_id">
                                    <option value="">Todos</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-3 mb-3">
                                <label for="module">Módulo</label>
                                <select class="form-control" id="module" name="module">
                                    <option value="">Todos</option>
                                    @foreach ($modules as $module)
                                        <option value="{{ $module }}"
                                            {{ request('module') == $module ? 'selected' : '' }}>
                                            {{ $module }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-3 mb-3 d-flex flex-wrap">
                                <button type="submit" class="btn btn-primary mr-2 mb-2">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                                <a href="{{ route('activity.logs') }}" class="btn btn-secondary mb-2">
                                    <i class="fa fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @php
                $pdfUrl = route('activity-logs.pdf', request()->all());
            @endphp

            <a href="{{ $pdfUrl }}" class="btn btn-danger mb-3" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> Generar reporte
            </a>

            <div class="card module-table-card bg-white has-shadow mb-4">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table module-table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Módulo</th>
                                    <th>Datos antes</th>
                                    <th>Datos despues</th>
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
                                                    Ver datos
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
                                                    Ver datos
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
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
