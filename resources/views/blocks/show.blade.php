@extends('layouts.app')

@section('title', 'Detalles del bloque')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Detalles del bloque</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('blocks.index') }}">Bloques</a></li>
            <li class="breadcrumb-item active">Detalles del bloque</li>
        </ul>
    </div>
    <section class="dashboard-counts no-padding-bottom">
        <a href="{{ route('blocks.index') }}" class="ml-4 mb-3 btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left-long"></i> Volver
        </a>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <h3 class="mb-0">Información del Bloque</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Usuario:</strong>
                                    {{ $block->user->name ?? 'Sin' }} {{ $block->user->last_name ?? 'Usuario' }}</li>
                                <li class="list-group-item"><strong>Número de documento:</strong>
                                    {{ $block->n_bloque }}
                                </li>
                                <li class="list-group-item"><strong>Asunto:</strong> {{ $block->asunto }}
                                </li>
                                <li class="list-group-item"><strong>Folios:</strong> {{ $block->folios }}
                                </li>
                                @if (Auth::user()->isAdminOrManager())
                                    <li class="list-group-item"><strong>Directorio del archivo:</strong>
                                        {{ $block->root }}</li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                @if ($block->box)
                                    <li class="list-group-item"><strong>Sección:</strong>
                                        {{ optional($block->box->andamio->section)->n_section }}
                                    </li>
                                    <li class="list-group-item"><strong>Andamio:</strong>
                                        {{ optional($block->box->andamio)->n_andamio }}
                                    </li>
                                    <li class="list-group-item"><strong>Caja:</strong>
                                        {{ optional($block->box)->n_box }}</li>
                                @else
                                    <li class="list-group-item"><strong>Ubicación:</strong> Este bloque no
                                        pertenece a ningúna caja.</li>
                                @endif
                                <li class="list-group-item"><strong>Periodo:</strong> {{ $year }}</li>
                                <li class="list-group-item"><strong>Mes:</strong> {{ $month }}</li>
                                <li class="list-group-item"><strong>Día:</strong> {{ $daySem }}
                                    {{ $day }}</li>
                                <li class="list-group-item"><strong>Àrea:</strong>
                                    {{ $block->group->areaGroupType->area->descripcion ?? 'Sin área' }}</li>
                                <li class="list-group-item"><strong>Grupo:</strong>
                                    {{ $block->group->descripcion ?? 'Sin grupo' }}</li>
                                <li class="list-group-item"><strong>Subgrupo:</strong>
                                    {{ $block->subgroup->descripcion ?? 'Sin subgrupo' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="mb-0">Vista Previa del Bloque</h3>
                </div>
                <div class="card-body">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe src="{{ asset('storage/' . $block->root) }}" frameborder="0" width="100%" height="600px"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
