@extends('layouts.app')

@section('title', 'Notificaciones')
@section('content')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Notificaciones</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Notificaciones</li>
        </ul>
    </div>
    <section class="dashboard-counts no-padding-bottom">
        <a href="{{ route('index') }}" class="ml-4 mb-3 btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left-long"></i> Volver
        </a>
        <!-- Module content-->
        <div class="container-fluid">
            <div class="bg-white has-shadow">
                @if ($notifications->isEmpty())
                    <p class="text-center text-muted p-4">No hay notificaciones disponibles.</p>
                @else
                    <ul class="list-group">
                        @foreach ($notifications as $notification)
                            <li class="list-group-item {{ $notification->read_at ? 'text-muted' : 'font-weight-bold' }}">
                                <p href="{{ route('documents.index') }}">
                                    {{ $notification->data['message'] }}
                                </p><small
                                    class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                            </li>
                        @endforeach
                    </ul>
                    <div class="p-3">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
