<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Actividades</title>
    <style>
        @page {
            margin: 50px 25px;
            size: A4 landscape;

            @top-right {
                font-size: 12px;
                font-weight: bold;
            }

            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 10px;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .title-header {
            background: #0D47A1;
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 12px;
            text-align: center;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 250px;
            position: absolute;
            left: 10px;
            top: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        p {
            text-align: center;
            font-size: 12px;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table-report {
            margin-top: 50px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
            font-size: 11px;
            word-wrap: break-word;
            white-space: normal;
        }

        th {
            background: #abcaed;
            text-transform: uppercase;
            font-weight: bold;
            padding: 10px;
        }

        .json-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .json-table th,
        .json-table td {
            border: 1px solid #aaa;
            padding: 4px;
            text-align: left;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ public_path('img/logo-ugel.png') }}" alt="Logo">
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
                <th>Acción</th>
                <th>Módulo</th>
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
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
                    <td>
                        <strong>Antes:</strong>
                        @if (is_array($log->before) && count($log->before))
                            <table class="json-table">
                                @foreach ($log->before as $key => $value)
                                    <tr>
                                        <th>{{ $key }}</th>
                                        <td>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif

                        <br>

                        <strong>Después:</strong>
                        @if (is_array($log->after) && count($log->after))
                            <table class="json-table">
                                @foreach ($log->after as $key => $value)
                                    <tr>
                                        <th>{{ $key }}</th>
                                        <td>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}
                                        </td>
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
