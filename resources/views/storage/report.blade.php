<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Almacenamiento</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #1e293b; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0f172a; padding-bottom: 10px; }
        .header img { max-width: 180px; height: auto; }
        .header h1 { font-size: 16px; margin: 5px 0; color: #0f172a; text-transform: uppercase; }
        .header p { font-size: 9px; color: #64748b; margin: 2px 0; }
        
        .stats-grid { width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 10px; }
        .stat-card { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; text-align: center; }
        .stat-card .number { font-size: 18px; font-weight: bold; color: #0f172a; margin-top: 4px; }
        .stat-card .label { font-size: 8px; font-weight: bold; color: #64748b; text-transform: uppercase; }
        
        .section-title { font-size: 11px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-top: 15px; margin-bottom: 8px; border-left: 3px solid #3b82f6; padding-left: 6px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #0f172a; color: #ffffff; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 6px; border: 1px solid #0f172a; text-align: left; }
        td { border: 1px solid #cbd5e1; padding: 5px; font-size: 9px; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .badge-success { color: #166534; background-color: #dcfce7; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; }
        .badge-warning { color: #854d0e; background-color: #fef9c3; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; }
        
        .footer { margin-top: 30px; font-size: 8px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>

    <div class="header">
        @if(file_exists(public_path('img/logo-ugel.png')))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo-ugel.png'))) }}" alt="Logo">
        @endif
        <h1>Reporte de Gestión de Almacén y Archivo Documental</h1>
        <p>Generado el: {{ $generatedAt }}</p>
    </div>

    <!-- TARJETAS DE RESUMEN -->
    <table class="stats-grid">
        <tr>
            <td class="stat-card" style="width: 33%;">
                <div class="label">Cajas Ocupadas / Llenas</div>
                <div class="number">{{ $stats['filledBoxes'] }} / {{ $stats['totalBoxes'] }}</div>
                <div style="font-size: 8px; color: #475569; margin-top: 2px;">{{ $stats['emptyBoxes'] }} Cajas vacías disponibles</div>
            </td>
            <td class="stat-card" style="width: 33%;">
                <div class="label">Bloques Archivados en Caja</div>
                <div class="number">{{ $stats['archivedBlocks'] }}</div>
                <div style="font-size: 8px; color: #475569; margin-top: 2px;">De {{ $stats['totalBlocks'] }} bloques en total</div>
            </td>
            <td class="stat-card" style="width: 33%;">
                <div class="label">Bloques Digitalizados / Indexados</div>
                <div class="number">{{ $stats['indexedBlocks'] }}</div>
                <div style="font-size: 8px; color: #475569; margin-top: 2px;">Con documento PDF adjunto</div>
            </td>
        </tr>
    </table>

    <!-- DESGLOSE POR ÁREA, GRUPO Y SUBGRUPO -->
    <div class="section-title">1. Resumen de Bloques por Área, Grupo y Subgrupo</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Área</th>
                <th style="width: 25%;">Grupo / Oficina</th>
                <th style="width: 25%;">Subgrupo</th>
                <th class="text-center" style="width: 8%;">Bloques</th>
                <th class="text-center" style="width: 8%;">Archivados</th>
                <th class="text-center" style="width: 9%;">Digitalizados</th>
                <th class="text-right" style="width: 10%;">Folios</th>
            </tr>
        </thead>
        <tbody>
            @forelse($areaBreakdown as $row)
                <tr>
                    <td>{{ $row['area'] }}</td>
                    <td>{{ $row['group'] }}</td>
                    <td>{{ $row['subgroup'] }}</td>
                    <td class="text-center font-bold">{{ $row['total_blocks'] }}</td>
                    <td class="text-center">{{ $row['archived_blocks'] }}</td>
                    <td class="text-center">{{ $row['indexed_blocks'] }}</td>
                    <td class="text-right font-bold">{{ number_format($row['total_folios']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No hay registros de bloques para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- INVENTARIO Y OCUPACIÓN DE CAJAS -->
    <div class="section-title">2. Inventario de Cajas de Almacenamiento</div>
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 10%;">N° Caja</th>
                <th style="width: 15%;">Sección</th>
                <th style="width: 15%;">Andamio</th>
                <th>Descripción / Observaciones</th>
                <th class="text-center" style="width: 12%;">Bloques Almacenados</th>
                <th class="text-right" style="width: 12%;">Total Folios</th>
                <th class="text-center" style="width: 12%;">Estatus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($boxesDetail as $box)
                <tr>
                    <td class="text-center font-bold">Caja {{ $box->n_box }}</td>
                    <td>Sección {{ $box->andamio?->section?->n_section ?? '-' }}</td>
                    <td>Andamio {{ $box->andamio?->n_andamio ?? '-' }}</td>
                    <td>{{ $box->descripcion ?: 'Sin descripción' }}</td>
                    <td class="text-center font-bold">{{ $box->blocks_count }}</td>
                    <td class="text-right">{{ number_format($box->blocks_sum_folios ?? 0) }}</td>
                    <td class="text-center">
                        @if($box->blocks_count > 0)
                            <span class="badge-success">OCUPADA</span>
                        @else
                            <span class="badge-warning">VACÍA</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No hay cajas registradas en el almacenamiento.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistema de Gestión Documental y Archivo Central &mdash; Documento generado automáticamente para fines de inventario.
    </div>

</body>
</html>
