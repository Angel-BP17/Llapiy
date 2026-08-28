<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios</title>
    <link rel="stylesheet" href="{{ resource_path('css/reporte.css') }}">
    <style>
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
        <div class="title">Reporte de Usuarios del Sistema</div>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="6" class="title-header">LISTADO DE USUARIOS</th>
            </tr>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Área</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $user->name }} {{ $user->last_name }}</td>
                    <td>{{ $user->user_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</td>
                    <td>{{ $user->group?->areaGroupType?->area?->descripcion ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No hay usuarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
