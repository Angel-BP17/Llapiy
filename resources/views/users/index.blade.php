@extends('layouts.app')

@section('title', 'Usuarios')
@section('content')
    @vite('resources/js/users/index.js')
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Usuarios</h2>
        </div>
    </header>
    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Usuarios</li>
        </ul>
    </div>

    <section class="forms module-ui">
        <!-- Filtros -->
        <div class="container-fluid mb-4">
            @php
                $roleLabels = [
                    'ADMINISTRADOR' => 'Administrador',
                    'admin' => 'Administrador',
                ];
                $oldCreateData = [
                    'area_id' => old('area_id'),
                    'group_type_id' => old('group_type_id'),
                    'group_id' => old('group_id'),
                    'subgroup_id' => old('subgroup_id'),
                ];
                $oldEditData = [
                    'area' => old('area'),
                    'groupType' => old('groupType'),
                    'group' => old('group'),
                    'subgroup' => old('subgroup'),
                ];
            @endphp
            <!-- Mostrar mensaje de Ã©xito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="card module-hero has-shadow mb-4">
                <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <div class="module-hero-chip mb-2">
                            <i class="fa-solid fa-users"></i> Gestion de usuarios
                        </div>
                        <h4 class="mb-1 text-white">Administracion de cuentas</h4>
                        <p class="mb-0 module-hero-text text-white-50">Controla usuarios, roles y organizacion desde un
                            mismo panel.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $users->total() }} registros
                        </span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-blue"><i class="fa-solid fa-users"></i></div>
                            <div>
                                <div class="text-muted small">Total de usuarios</div>
                                <div class="h4 mb-0">{{ $totalUsers }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-violet"><i class="fa-solid fa-user-shield"></i></div>
                            <div>
                                <div class="text-muted small">Roles disponibles</div>
                                <div class="h4 mb-0">{{ $totalRoles }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-green"><i class="fa-solid fa-sitemap"></i></div>
                            <div>
                                <div class="text-muted small">Áreas registradas</div>
                                <div class="h4 mb-0">{{ $totalAreas }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de usuarios -->
            <button class="mb-3 btn btn-success" type="button" data-toggle="modal" data-target="#userCreateModal">
                <i class="fa-solid fa-user-plus"></i> Crear Usuario
            </button>

            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body">
                    <h4>Buscar</h4>
                    <form method="GET" action="{{ route('users.index') }}" class="row align-items-end">
                        <div class="col-12 col-md-4 mb-3">
                            <label for="search" class="sr-only">Nombre o Apellido</label>
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Nombre o apellido">
                        </div>
                        <div class="col-12 col-md-auto mb-3">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i>
                                Aplicar filtros</button>
                        </div>
                        <div class="col-12 col-md-auto mb-3">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fa-solid fa-x"></i>
                                Limpiar
                                filtros</a>
                        </div>

                        @php
                            $pdfUrl = route('users.pdf', request()->all());
                        @endphp

                        <div class="col-12 col-md-auto">
                            <a href="{{ $pdfUrl }}" class="btn btn-danger mb-3 " target="_blank">
                                <i class="fa-solid fa-file-pdf"></i> Generar Reporte
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card module-table-card bg-white has-shadow mb-4">
                <div class="table-responsive">
                    <table class="table module-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Foto</th>
                                <th>Correo</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <th scope="row">{{ $user->id }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>
                                        @php
                                            $avatar = $user->foto_perfil
                                                ? asset('storage/' . $user->foto_perfil)
                                                : asset('img/default-avatar.png');
                                            $userData = [
                                                'id' => $user->id,
                                                'name' => $user->name,
                                                'last_name' => $user->last_name,
                                                'user_name' => $user->user_name,
                                                'email' => $user->email,
                                                'dni' => $user->dni,
                                                'foto' => $avatar,
                                                'roles' => $user->roles->pluck('name')->values(),
                                                'group_id' => $user->group_id,
                                                'subgroup_id' => $user->subgroup_id,
                                                'group' => $user->group
                                                    ? [
                                                        'id' => $user->group->id,
                                                        'descripcion' => $user->group->descripcion,
                                                        'area_group_type' => [
                                                            'area' => $user->group->areaGroupType?->area
                                                                ? [
                                                                    'id' => $user->group->areaGroupType->area->id,
                                                                    'descripcion' =>
                                                                        $user->group->areaGroupType->area->descripcion,
                                                                ]
                                                                : null,
                                                            'group_type' => $user->group->areaGroupType?->groupType
                                                                ? [
                                                                    'id' => $user->group->areaGroupType->groupType->id,
                                                                    'descripcion' =>
                                                                        $user->group->areaGroupType->groupType
                                                                            ->descripcion,
                                                                ]
                                                                : null,
                                                        ],
                                                    ]
                                                    : null,
                                                'subgroup' => $user->subgroup
                                                    ? [
                                                        'id' => $user->subgroup->id,
                                                        'descripcion' => $user->subgroup->descripcion,
                                                    ]
                                                    : null,
                                            ];
                                        @endphp
                                        <img src="{{ $avatar }}" alt="Foto de perfil" width="35" height="35"
                                            class="rounded-circle">
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1 flex-wrap">
                                            <button type="button" class="btn btn-info btn-sm js-user-show"
                                                data-toggle="modal" data-target="#userShowModal"
                                                data-user='@json($userData)'>
                                                <i class="fa-solid fa-eye"></i> Ver
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm js-user-edit"
                                                data-toggle="modal" data-target="#userEditModal"
                                                data-user='@json($userData)'>
                                                <i class="fa-solid fa-pen"></i> Editar
                                            </button>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Â¿Eliminar este usuario? Se borrarÃ¡ permanentemente')">
                                                    <i class="fa-solid fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron usuarios.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- PaginaciÃ³n -->
            <div class="d-flex justify-content-center">
                {{ $users->links() }}
            </div>

            <!-- Modales -->
            <div class="modal fade" id="userCreateModal" tabindex="-1" role="dialog"
                aria-labelledby="userCreateLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userCreateLabel">Crear usuario</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_modal" value="create">
                            <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                                @if ($errors->any() && old('_modal') === 'create')
                                    <div class="alert alert-danger">
                                        <i class="fa-solid fa-triangle-exclamation mr-2" aria-hidden="true"></i>
                                        <strong>Revisa los campos:</strong>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-12 col-lg-4 mb-4">
                                        <div class="card bg-white has-shadow h-100">
                                            <div class="card-body d-flex align-items-center justify-content-center">
                                                <div class="col">
                                                    <h6 class="mb-3"><i class="fa-solid fa-image-portrait mr-2"></i>Foto
                                                        de
                                                        perfil</h6>
                                                    <div class="text-center">
                                                        <img id="create_preview"
                                                            src="{{ asset('img/default-avatar.png') }}" alt="Preview"
                                                            class="rounded-circle mb-3" width="150" height="150"
                                                            style="object-fit: cover;">
                                                        <div class="custom-file">
                                                            <input class="custom-file-input" type="file"
                                                                id="create_foto_perfil" name="foto_perfil"
                                                                accept="image/*">
                                                            <label class="custom-file-label"
                                                                for="create_foto_perfil">Seleccionar
                                                                imagen</label>
                                                        </div>
                                                        <small class="text-muted d-block mt-2">PNG/JPG hasta 2MB.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-8">
                                        <div class="card bg-white has-shadow">
                                            <div class="card-body">
                                                <h6 class="mb-3"><i class="fa-solid fa-id-card mr-2"></i>Datos
                                                    personales</h6>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="create_name">Nombres</label>
                                                        <input id="create_name" name="name" type="text"
                                                            value="{{ old('name') }}" class="form-control">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="create_last_name">Apellidos</label>
                                                        <input id="create_last_name" name="last_name" type="text"
                                                            value="{{ old('last_name') }}" class="form-control">
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label for="create_dni">DNI</label>
                                                        <input id="create_dni" name="dni" type="text"
                                                            value="{{ old('dni') }}" class="form-control">
                                                    </div>
                                                </div>

                                                <h6 class="mb-3 mt-4"><i class="fa-solid fa-user-gear mr-2"></i>Cuenta
                                                </h6>
                                                <div class="form-row">
                                                    <div class="form-group col-md-4">
                                                        <label for="create_user_name">Usuario</label>
                                                        <input id="create_user_name" name="user_name" type="text"
                                                            value="{{ old('user_name') }}" class="form-control">
                                                    </div>
                                                    <div class="form-group col-md-5">
                                                        <label for="create_email">Correo</label>
                                                        <input id="create_email" name="email" type="email"
                                                            value="{{ old('email') }}" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="create_password">Contraseña</label>
                                                        <input id="create_password" name="password" type="password"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="create_password_confirmation">Confirmar
                                                            contraseña</label>
                                                        <input id="create_password_confirmation"
                                                            name="password_confirmation" type="password"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <h6 class="mb-3 mt-4"><i class="fa-solid fa-user-shield mr-2"></i>Roles
                                                </h6>
                                                <div class="form-row">
                                                    <div class="form-group col-md-12">
                                                        <div class="d-flex flex-wrap">
                                                            @foreach ($roles as $role)
                                                                <label class="mr-3 mb-2">
                                                                    <input type="checkbox" name="roles[]"
                                                                        value="{{ $role->name }}"
                                                                        @checked(collect(old('roles', []))->contains($role->name))>
                                                                    <span
                                                                        class="ml-1">{{ $roleLabels[$role->name] ?? $role->name }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        <small class="text-muted d-block mt-1">Puedes seleccionar múltiples
                                                            roles.</small>
                                                    </div>
                                                </div>

                                                <h6 class="mb-3 mt-4"><i class="fa-solid fa-sitemap mr-2"></i>Organización
                                                </h6>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="create_area_id">Área</label>
                                                        <select id="create_area_id" name="area_id" class="form-control">
                                                            <option value="">Seleccione</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="create_group_type_id">Tipo de Grupo</label>
                                                        <select id="create_group_type_id" name="group_type_id"
                                                            class="form-control" disabled>
                                                            <option value="">Seleccione un tipo de grupo</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="create_group_id">Grupo</label>
                                                        <select id="create_group_id" name="group_id" class="form-control"
                                                            disabled>
                                                            <option value="">Seleccione un grupo</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="create_subgroup_id">Subgrupo</label>
                                                        <select id="create_subgroup_id" name="subgroup_id"
                                                            class="form-control" disabled>
                                                            <option value="">Seleccione un subgrupo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="userEditModal" tabindex="-1" role="dialog" aria-labelledby="userEditLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userEditLabel">Editar usuario</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="userEditForm" action="{{ route('users.update', ['user' => 'USER_ID']) }}"
                            data-action-template="{{ route('users.update', ['user' => 'USER_ID']) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_modal" value="edit">
                            <input type="hidden" name="edit_user_id" id="edit_user_id"
                                value="{{ old('edit_user_id') }}">
                            <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                                @if ($errors->any() && old('_modal') === 'edit')
                                    <div class="alert alert-danger">
                                        <i class="fa-solid fa-triangle-exclamation mr-2" aria-hidden="true"></i>
                                        <strong>Revisa los campos:</strong>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-12 col-lg-4 mb-4">
                                        <div class="card bg-white has-shadow h-100">
                                            <div class="card-body d-flex align-items-center justify-content-center">
                                                <div class="col">
                                                    <h6 class="mb-3"><i class="fa-solid fa-image-portrait mr-2"></i>Foto
                                                        de
                                                        perfil</h6>
                                                    <div class="text-center">
                                                        <img id="edit_preview"
                                                            src="{{ asset('img/default-avatar.png') }}" alt="Preview"
                                                            class="rounded-circle mb-3" width="150" height="150"
                                                            style="object-fit: cover;">
                                                        <div class="custom-file">
                                                            <input class="custom-file-input" type="file"
                                                                id="edit_foto_perfil" name="foto_perfil"
                                                                accept="image/*">
                                                            <label class="custom-file-label"
                                                                for="edit_foto_perfil">Cambiar
                                                                imagen</label>
                                                        </div>
                                                        <small class="text-muted d-block mt-2">PNG/JPG hasta 2MB.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-8">
                                        <div class="card bg-white has-shadow">
                                            <div class="card-body">
                                                <h6 class="mb-3"><i class="fa-solid fa-id-card mr-2"></i>Datos
                                                    personales</h6>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="edit_name">Nombres</label>
                                                        <input id="edit_name" name="name" type="text"
                                                            class="form-control" value="{{ old('name') }}">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="edit_last_name">Apellidos</label>
                                                        <input id="edit_last_name" name="last_name" type="text"
                                                            class="form-control" value="{{ old('last_name') }}">
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label for="edit_dni">DNI</label>
                                                        <input id="edit_dni" name="dni" type="text"
                                                            class="form-control" value="{{ old('dni') }}">
                                                    </div>
                                                </div>

                                                <h6 class="mb-3 mt-4"><i class="fa-solid fa-user-gear mr-2"></i>Cuenta
                                                </h6>
                                                <div class="form-row">
                                                    <div class="form-group col-md-4">
                                                        <label for="edit_user_name">Usuario</label>
                                                        <input id="edit_user_name" name="user_name" type="text"
                                                            class="form-control" value="{{ old('user_name') }}">
                                                    </div>
                                                    <div class="form-group col-md-5">
                                                        <label for="edit_email">Correo</label>
                                                        <input id="edit_email" name="email" type="email"
                                                            class="form-control" value="{{ old('email') }}">
                                                    </div>
                                                </div>

                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="edit_password">Nueva contraseña <small
                                                                class="text-muted">(opcional)</small></label>
                                                        <input id="edit_password" name="password" type="password"
                                                            class="form-control" placeholder="Dejar en blanco">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="edit_password_confirmation">Confirmar
                                                            contraseña</label>
                                                        <input id="edit_password_confirmation"
                                                            name="password_confirmation" type="password"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <h6 class="mb-3 mt-4"><i class="fa-solid fa-user-shield mr-2"></i>Roles
                                                </h6>
                                                <div class="form-row">
                                                    <div class="form-group col-md-12">
                                                        <div class="d-flex flex-wrap" id="edit_roles_container">
                                                            @foreach ($roles as $role)
                                                                <label class="mr-3 mb-2">
                                                                    <input type="checkbox" name="roles[]"
                                                                        value="{{ $role->name }}">
                                                                    <span
                                                                        class="ml-1">{{ $roleLabels[$role->name] ?? $role->name }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        <small class="text-muted d-block mt-1">Puedes seleccionar múltiples
                                                            roles.</small>
                                                    </div>
                                                </div>

                                                <h6 class="mb-3 mt-4"><i class="fa-solid fa-sitemap mr-2"></i>Organización
                                                </h6>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="edit_area">Área</label>
                                                        <select id="edit_area" name="area" class="form-control">
                                                            <option value="">Seleccione</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="edit_group_type">Tipo de Grupo</label>
                                                        <select id="edit_group_type" name="groupType"
                                                            class="form-control" required>
                                                            <option value="">Seleccione un tipo de grupo</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="edit_group">Grupo</label>
                                                        <select id="edit_group" name="group" class="form-control">
                                                            <option value="">Seleccione un grupo</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="edit_subgroup">Subgrupo</label>
                                                        <select id="edit_subgroup" name="subgroup" class="form-control">
                                                            <option value="">Seleccione un subgrupo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                                    Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="userShowModal" tabindex="-1" role="dialog" aria-labelledby="userShowLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userShowLabel">Detalle de usuario</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex align-items-center mb-3">
                                <img id="show_avatar" src="{{ asset('img/default-avatar.png') }}" alt="Foto"
                                    class="rounded-circle mr-3" width="72" height="72"
                                    style="object-fit: cover;">
                                <div>
                                    <h5 class="mb-0" id="show_name">—</h5>
                                    <div class="text-muted small" id="show_username">—</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="text-muted small">Correo</div>
                                    <div class="font-weight-bold" id="show_email">—</div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="text-muted small">DNI</div>
                                    <div class="font-weight-bold" id="show_dni">—</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="text-muted small">Roles</div>
                                    <div id="show_roles"></div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="text-muted small">Área</div>
                                    <div class="font-weight-bold" id="show_area">—</div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="text-muted small">Grupo</div>
                                    <div class="font-weight-bold" id="show_group">—</div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="text-muted small">Subgrupo</div>
                                    <div class="font-weight-bold" id="show_subgroup">—</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="users-page-data" class="d-none" data-areas='@json($areas)'
                data-old-modal='@json(old('_modal'))' data-old-edit-user-id='@json(old('edit_user_id'))'
                data-old-edit-roles='@json(old('roles', []))' data-old-create='@json($oldCreateData)'
                data-old-edit='@json($oldEditData)' data-role-labels='@json($roleLabels)'
                data-default-avatar="{{ asset('img/default-avatar.png') }}"></div>

        </div>
    </section>
@endsection
