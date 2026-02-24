<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios</title>
    @include('reports.partials.styles')
</head>

<body>
    <div class="header">
        @include('reports.partials.header_logo')
        <div class="title">Reporte de Usuarios</div>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="6" class="title-header">USUARIOS DETALLE</th>
            </tr>
            <tr>
                <th>ID</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>DNI</th>
                <th>Email</th>
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
                    <td>{{ optional($user->created_at)->format('d/m/Y') ?? '-' }}</td>
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

