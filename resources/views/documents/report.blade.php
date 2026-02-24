<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Documentos</title>
    @include('reports.partials.styles')
</head>

<body>
    <div class="header">
        @include('reports.partials.header_logo')
        <div class="title">Reporte de Documentos</div>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="10" class="title-header">INVENTARIO DETALLE</th>
            </tr>
            <tr>
                <th>ID</th>
                <th>N° de documento</th>
                <th>Asunto</th>
                <th>Folios</th>
                <th>Tipo Documental</th>
                <th>Área</th>
                <th>Sección</th>
                <th>Andamio</th>
                <th>Caja</th>
                <th>Paquete</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $index => $document)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $document->n_documento }}</td>
                    <td>{{ $document->asunto }}</td>
                    <td>{{ $document->folios }}</td>
                    <td>{{ optional($document->documentType)->name ?? '-' }}</td>
                    <td>{{ data_get($document, 'group.areaGroupType.area.descripcion', '-') }}</td>

                    @if ($document->box)
                        <td>{{ optional($document->box->andamio->section)->n_section ?? '-' }}</td>
                        <td>{{ optional($document->box->andamio)->n_andamio ?? '-' }}</td>
                        <td>{{ optional($document->box)->n_box ?? '-' }}</td>
                        <td>-</td>
                    @else
                        <td colspan="4">-</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="10">No hay documentos para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
