<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Bloques</title>
    <link rel="stylesheet" href="{{ resource_path('css/reporte.css') }}">
    <style>
        /* Fallback en caso de que el archivo CSS no cargue correctamente */
        body { font-family: Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; text-align: center; }
        th { background-color: #abcaed; }
        .title-header { background-color: #0d47a1; color: white; font-weight: bold; }
    </style>
</head>

<body>
    <div class="header">
        @if(file_exists(public_path('img/logo-ugel.png')))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo-ugel.png'))) }}" alt="Logo" style="width: 200px;">
        @endif
        <div class="title">Reporte de Bloques</div>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="8" class="title-header">INVENTARIO DETALLE</th>
            </tr>
            <tr>
                <th>ID</th>
                <th>N° de bloque</th>
                <th>Asunto</th>
                <th>Folios</th>
                <th>Área</th>
                <th>Sección</th>
                <th>Andamio</th>
                <th>Caja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($blocks as $index => $block)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $block->n_bloque }}</td>
                    <td style="text-align: left;">{{ $block->asunto }}</td>
                    <td>{{ $block->folios }}</td>
                    <td>{{ $block->group?->areaGroupType?->area?->descripcion ?? '-' }}</td>

                    @if ($block->box)
                        <td>{{ $block->box->andamio?->section?->n_section ?? '-' }}</td>
                        <td>{{ $block->box->andamio?->n_andamio ?? '-' }}</td>
                        <td>{{ $block->box->n_box ?? '-' }}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="8">No hay bloques para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
