@extends('layouts.app')

@section('title', 'Inicio')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col">
                    <h2 class="no-margin-bottom">Inicio</h2>
                </div>
                <div class="col-auto">
                    @if (Auth::user()->can('clear-system'))
                        <form method="POST" action="{{ route('admin.clear_all') }}"
                            onsubmit="return confirm('¿Estás seguro de eliminar todos los datos? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm ml-4 no-margin-bottom">
                                <i class="fa-solid fa-trash"></i> Borrar todos los datos
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </header>
    <!-- Dashboard Contadores por seccion-->
    <section class="dashboard-counts no-padding-bottom">
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
        <div class="container-fluid">
            <div class="row bg-white has-shadow">
                @if (Auth::user()->userType->name === 'Administrador')
                    <!-- Item -->
                    <div class="col-xl-3 col-sm-6">
                        <div class="item d-flex align-items-center">
                            <div class="icon bg-violet"><i class="icon-user"></i></div>
                            <div class="title"><a href="{{ route('users.index') }}">Usuarios <br>Registrados</a>
                            </div>
                            <div class="number"><strong>{{ $userCount }}</strong></div>
                        </div>
                    </div>
                @endif
                <!-- Item -->
                <div class="col-xl-3 col-sm-6">
                    <div class="item d-flex align-items-center">
                        <div class="icon bg-red"><i class="icon-padnote"></i></div>
                        <div class="title"><a href="{{ route('documents.index') }}">Archivos <br>Registrados</a>
                        </div>
                        <div class="number"><strong>{{ $documentCount }}</strong></div>
                    </div>
                </div>
                <!-- Item -->
                <div class="col-xl-3 col-sm-6">
                    <div class="item d-flex align-items-center">
                        <div class="icon bg-green"><i class="icon-bill"></i></div>
                        <div class="title"><a href="{{ route('inbox.index') }}">Bloques <br>no almacenados</a>
                        </div>
                        <div class="number"><strong>{{ $totalNoAlmacenados }}</strong></div>
                    </div>
                </div>
                <!-- Item -->
                <div class="col-xl-3 col-sm-6">
                    <div class="item d-flex align-items-center">
                        <div class="icon bg-orange"><i class="icon-check"></i></div>
                        <div class="title"><a href="{{ route('document_types.index') }}">Tipos de <br>documentos</a>
                        </div>
                        <div class="number"><strong>{{ $documentTypeCount }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if (Auth::user()->userType->name === 'Administrador')
        <!-- Dashboard Header Section    -->
        <section class="dashboard-header">
            <div class="container-fluid">
                <div class="row">
                    <!-- Line Chart-->
                    <div class="chart col-lg-5 col-12">
                        <div class="line-chart bg-white d-flex align-items-center justify-content-center has-shadow">
                            <canvas id="lineCahrt"></canvas>
                        </div>
                    </div>
                    <div class="chart col-lg-4 col-12">
                        <!-- Bar Chart   -->
                        <div class="bar-chart has-shadow bg-white">
                            <div class="title"><strong class="text-violet">Documentos ingresados por
                                    Mes</strong>
                            </div>
                            <canvas id="barChartHome" height="300"></canvas>
                        </div>
                    </div>
                    <div class="chart col-lg-3 col-12">
                        <div class="work-amount card">
                            <div class="card-body">
                                <h3>Tipos de documentos</h3><small>Porcentaje de tipos de documentos</small>
                                <div class="chart text-center">
                                    <canvas id="pieChart" height="220"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    @if (Auth::user()->userType->name !== 'Administrador')
        <section class="dashboard-header">
            <div class="container-fluid">
                <div class="p-5 bg-white has-shadow">
                    <img class="rounded mx-auto d-block" height="350" src="{{ asset('img/logo.png') }}" alt="">
                </div>

            </div>
        </section>
    @endif
    <script>
        window.dashboardData = {
            documentosRecientes: @json($documentosRecientes ?? []),
            documentosPorTipo: @json($documentosPorTipo ?? []),
            documentosPorMes: @json($documentosPorMes ?? [])
        };
    </script>
    <script src="{{ asset('js/charts-home.js') }}"></script>
@endsection
