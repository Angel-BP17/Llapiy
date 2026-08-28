<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Documentos</title>
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
                    <td style="text-align: left;">{{ $document->asunto }}</td>
                    <td>{{ $document->folios }}</td>
                    <td>{{ $document->documentType?->name ?? '-' }}</td>
                    <td>{{ $document->group?->areaGroupType?->area?->descripcion ?? '-' }}</td>

                    @if ($document->box)
                        <td>{{ $document->box->andamio?->section?->n_section ?? '-' }}</td>
                        <td>{{ $document->box->andamio?->n_andamio ?? '-' }}</td>
                        <td>{{ $document->box->n_box ?? '-' }}</td>
                        <td>{{ $document->box->paquete ?? '-' }}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
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
