<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Actividades</title>
    <link rel="stylesheet" href="{{ resource_path('css/reporte.css') }}">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; text-align: center; }
        th { background-color: #abcaed; }
        .title-header { background-color: #0d47a1; color: white; font-weight: bold; }
        .text-left { text-align: left; }
    </style>
</head>

<body>
    <div class="header">
        @if(file_exists(public_path('img/logo-ugel.png')))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo-ugel.png'))) }}" alt="Logo" style="width: 200px;">
        @endif
        <div class="title">Reporte de Actividades del Sistema</div>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="5" class="title-header">LOG DE AUDITORÍA</th>
            </tr>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Módulo / Modelo</th>
                <th>Fecha y Hora</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->user?->name }} {{ $log->user?->last_name }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ str_replace('App\\Models\\', '', $log->model) }}</td>
                    <td>{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay actividades registradas en el periodo seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
