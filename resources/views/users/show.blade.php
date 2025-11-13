@extends('layouts.app')

@section('title', 'Perfil de usuario')

@section('content')
    <!-- Page Header -->
    <header class="page-header">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <h2 class="no-margin-bottom">Perfil de usuario</h2>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
            <li class="breadcrumb-item active">Detalle</li>
        </ul>
    </div>

    <section class="dashboard-counts no-padding-bottom">
        <div class="container-fluid">

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="fa-solid fa-circle-check mr-2" aria-hidden="true"></i>
                    {{ session('success') }}
                </div>
            @endif

            <a href="{{ route('users.index') }}" class="mb-3 btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left-long" aria-hidden="true"></i> Volver
            </a>

            <div class="row">
                <!-- Columna izquierda: Perfil -->
                <div class="col-md-4 mb-4">
                    <div class="card bg-white has-shadow">
                        <div class="card-body text-center">
                            @php
                                $avatar = $user->foto_perfil
                                    ? asset('storage/' . $user->foto_perfil)
                                    : asset('img/default-avatar.png');
                            @endphp
                            <img src="{{ $avatar }}" alt="Foto de {{ $user->name }} {{ $user->last_name }}"
                                width="120" height="120" class="rounded-circle mb-3" style="object-fit: cover;">

                            <h4 class="mb-1">{{ $user->name }} {{ $user->last_name }}</h4>
                            <div class="mb-2">
                                <span class="badge badge-pill badge-primary">
                                    <i class="fa-solid fa-shield-halved mr-1" aria-hidden="true"></i>
                                    {{ $user->userType->name ?? '—' }}
                                </span>
                            </div>
                            <div class="text-muted small mb-3">
                                <i class="fa-solid fa-at mr-1" aria-hidden="true"></i>{{ $user->user_name ?? '—' }}
                            </div>

                            <div class="btn-group" role="group" aria-label="Acciones principales">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                    Editar
                                </a>
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa-solid fa-list" aria-hidden="true"></i>
                                    Todos
                                </a>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-id-card mr-2" aria-hidden="true"></i>
                                <strong class="mr-1">DNI:</strong>
                                <span class="text-muted">{{ $user->dni ?? '—' }}</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-envelope mr-2" aria-hidden="true"></i>
                                <strong class="mr-1">Correo:</strong>
                                @if ($user->email)
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </li>
                        </ul>
                    </div>

                    <div class="card bg-white has-shadow mt-4">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fa-solid fa-chart-simple mr-2" aria-hidden="true"></i>
                                Actividad
                            </h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Documentos registrados</span>
                                <span class="font-weight-bold">{{ $user->documents()->count() }}</span>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fa-solid fa-clock mr-1" aria-hidden="true"></i>
                                Creado: {{ optional($user->created_at)->format('d/m/Y H:i') ?? '—' }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>
                                Actualizado: {{ optional($user->updated_at)->format('d/m/Y H:i') ?? '—' }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Información -->
                <div class="col-md-8 mb-4">
                    <div class="card bg-white has-shadow mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-circle-info mr-2" aria-hidden="true"></i>
                                Información general
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <div class="text-muted small">Nombres</div>
                                    <div class="font-weight-bold">{{ $user->name ?? '—' }}</div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="text-muted small">Apellidos</div>
                                    <div class="font-weight-bold">{{ $user->last_name ?? '—' }}</div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="text-muted small">Tipo de usuario</div>
                                    <div class="font-weight-bold">{{ $user->userType->name ?? '—' }}</div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="text-muted small">Usuario</div>
                                    <div class="font-weight-bold">{{ $user->user_name ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-white has-shadow">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-sitemap mr-2" aria-hidden="true"></i>
                                Organización
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <div class="text-muted small">Área</div>
                                    <div class="font-weight-bold">
                                        {{ $user->group->areaGroupType->area->descripcion ?? 'Sin área' }}
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="text-muted small">Grupo</div>
                                    <div class="font-weight-bold">
                                        {{ $user->group->descripcion ?? 'Sin grupo' }}
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="text-muted small">Subgrupo</div>
                                    <div class="font-weight-bold">
                                        {{ $user->subgroup->descripcion ?? 'Sin subgrupo' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $recentDocuments = $user->documents()->latest()->limit(5)->get();
                    @endphp
                    @if ($recentDocuments->count())
                        <div class="card bg-white has-shadow mt-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fa-solid fa-file-lines mr-2" aria-hidden="true"></i>
                                    Últimos documentos
                                </h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tipo</th>
                                            <th>Asunto</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentDocuments as $doc)
                                            <tr>
                                                <td>{{ $doc->id }}</td>
                                                <td>{{ $doc->documentType->name ?? '—' }}</td>
                                                <td class="text-truncate" style="max-width: 360px;">
                                                    {{ $doc->asunto }}
                                                </td>
                                                <td>{{ optional($doc->fecha)->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
