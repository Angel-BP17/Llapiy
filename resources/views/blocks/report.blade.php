<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Bloques</title>
    <link rel="stylesheet" href="file://{{ base_path('public/css/reporte.css') }}">
</head>

<body>
    <div class="header">
        <img src="{{ public_path('img/logo-ugel.png') }}" alt="Logo">
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
                    <td>{{ $block->asunto }}</td>
                    <td>{{ $block->folios }}</td>
                    <td>{{ optional($block->group->areaGroupType->area)->descripcion ?? '-' }}</td>

                    @if ($block->box)
                        <td>{{ optional($block->box->andamio->section)->n_section ?? '-' }}</td>
                        <td>{{ optional($block->box->andamio)->n_andamio ?? '-' }}</td>
                        <td>{{ optional($block->box)->n_box ?? '-' }}</td>
                    @else
                        <td colspan="3">-</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="8">No hay documentos para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
