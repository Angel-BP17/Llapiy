<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Actividades</title>
    @include('reports.partials.styles')
    <style>
        .table-report {
            margin-top: 50px;
        }
    </style>
</head>

<body>

    <div class="header">
        @include('reports.partials.header_logo')
        <div class="title">Reporte de Actividades</div>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table class="table-report">
        <thead>
            <tr>
                <th colspan="5" class="title-header">REGISTRO DE ACTIVIDADES</th>
            </tr>
            <tr>
                <th>Usuario</th>
                <th>Accion</th>
                <th>Modulo</th>
                <th>Fecha</th>
                <th>Datos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ optional($log->user)->name ?? '-' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ str_replace('App\\Models\\', '', $log->model) }}</td>
                    <td>{{ optional($log->created_at)->format('d/m/Y H:i:s') ?? '-' }}</td>
                    <td>
                        <strong>Antes:</strong>
                        @if (is_array($log->before) && count($log->before))
                            <table class="json-table">
                                @foreach ($log->before as $key => $value)
                                    <tr>
                                        <th>{{ $key }}</th>
                                        <td>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif

                        <br>

                        <strong>Despues:</strong>
                        @if (is_array($log->after) && count($log->after))
                            <table class="json-table">
                                @foreach ($log->after as $key => $value)
                                    <tr>
                                        <th>{{ $key }}</th>
                                        <td>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay actividades registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
