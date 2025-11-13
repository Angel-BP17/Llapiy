<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios</title>
    <link rel="stylesheet" href="file://{{ base_path('public/css/reporte.css') }}">
</head>

<body>
    <div class="header">
        <img src="{{ public_path('img/logo-ugel.png') }}" alt="Logo">
        <div class="title">Reporte de Usuarios</div>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="7" class="title-header">USUARIOS DETALLE</th>
            </tr>
            <tr>
                <th>ID</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>DNI</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->last_name }}</td>
                    <td>{{ $user->dni }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ optional($user->userType)->name ?? '-' }}</td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay usuarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
