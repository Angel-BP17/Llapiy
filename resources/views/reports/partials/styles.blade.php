@php
    $cssPath = public_path('css/reporte.css');
    $css = is_readable($cssPath) ? file_get_contents($cssPath) : null;
@endphp

@if ($css)
    <style>
        {!! $css !!}
    </style>
@else
    <style>
        @page {
            margin: 50px 25px;
            size: A4 landscape;
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
            margin-top: 25px;
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
    </style>
@endif
