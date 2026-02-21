@extends('layouts.app')

@section('title', 'Roles')
@section('content')
    @vite('resources/js/roles/index.js')
    @php
        $roleNameLabels = [
            'ADMINISTRADOR' => 'Administrador',
            'admin' => 'Administrador',
        ];
    @endphp

    <header class="page-header">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <div>
                <h2 class="no-margin-bottom">Roles y permisos</h2>
                <small class="text-muted">Administra roles y su acceso a modulos del sistema</small>
            </div>
            <button class="btn btn-success" type="button" data-toggle="modal" data-target="#roleCreateModal">
                <i class="fa-solid fa-user-shield"></i> Crear Rol
            </button>
        </div>
    </header>

    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active">Roles</li>
        </ul>
    </div>

    <section class="forms module-ui">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card module-hero has-shadow mb-4">
                <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <div class="module-hero-chip mb-2">
                            <i class="fa-solid fa-user-shield"></i> Administracion de acceso
                        </div>
                        <h4 class="mb-1 text-white">Roles y permisos</h4>
                        <p class="mb-0 module-hero-text text-white-50">Gestiona permisos por rol para controlar la operacion de cada modulo.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="module-hero-chip">
                            <i class="fa-solid fa-list"></i> {{ $roles->total() }} registros
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-violet"><i class="fa-solid fa-users-gear"></i></div>
                            <div>
                                <div class="text-muted small">Total de roles</div>
                                <div class="h4 mb-0">{{ $totalRoles }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card module-stat-card bg-white has-shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon mr-3 text-blue"><i class="fa-solid fa-key"></i></div>
                            <div>
                                <div class="text-muted small">Permisos disponibles</div>
                                <div class="h4 mb-0">{{ $totalPermissions }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card module-filter-card bg-white has-shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('roles.index') }}" class="row align-items-end">
                        <div class="col-md-6 col-lg-4 mb-2">
                            <label for="search" class="sr-only">Buscar roles</label>
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Buscar por nombre del rol">
                        </div>
                        <div class="col-auto mb-2">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
                        </div>
                        <div class="col-auto mb-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-x"></i> Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card module-table-card bg-white has-shadow mb-4">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Lista de roles</h5>
                    <small class="text-muted">{{ $roles->total() }} registro(s)</small>
                </div>
                <div class="table-responsive">
                    <table class="table module-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Permisos</th>
                                <th class="text-end align-middle">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td class="font-weight-bold">{{ $roleNameLabels[$role->name] ?? $role->name }}</td>
                                    <td>
                                        @php
                                            $rolePerms = $role->permissions->pluck('name');
                                        @endphp
                                        @forelse ($rolePerms as $perm)
                                            <span class="badge bg-secondary me-1 mb-1 text-white">{{ $permissionLabels[$perm] ?? $perm }}</span>
                                        @empty
                                            <span class="text-muted">Sin permisos</span>
                                        @endforelse
                                    </td>
                                    <td class="text-end align-middle">
                                        <div class="d-flex align-items-center justify-content-end gap-1 flex-wrap">
                                            @php
                                                $roleData = [
                                                    'id' => $role->id,
                                                    'name' => $role->name,
                                                    'permissions' => $role->permissions->pluck('name')->values(),
                                                ];
                                            @endphp
                                            <button type="button" class="btn btn-warning btn-sm js-role-edit"
                                                data-toggle="modal" data-target="#roleEditModal"
                                                data-role='@json($roleData)'>
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <form action="{{ route('roles.destroy', $role) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar este rol?')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No se encontraron roles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">{{ $roles->links() }}</div>
            </div>

            <div class="modal fade" id="roleCreateModal" tabindex="-1" role="dialog" aria-labelledby="roleCreateLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="roleCreateLabel">Crear rol</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_modal" value="create">
                            <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                                @if ($errors->any() && old('_modal') === 'create')
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="role_create_name">Nombre del rol</label>
                                    <input type="text" name="name" id="role_create_name" class="form-control" value="{{ old('name') }}" required>
                                </div>

                                <div class="permissions-section">
                                    <div class="permissions-toolbar">
                                        <p class="permissions-title mb-0">Permisos</p>
                                        <div class="permissions-actions">
                                            <span class="badge badge-pill badge-primary" id="role_create_perm_count">0</span>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="role_create_perm_expand_all">Expandir todo</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="role_create_perm_collapse_all">Contraer todo</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="role_create_perm_select_all">Seleccionar todo</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="role_create_perm_clear">Limpiar</button>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="text" class="form-control form-control-sm permission-search-input" id="role_create_perm_search" placeholder="Buscar modulo o permiso...">
                                    </div>
                                    <div id="role_create_perm_selected" class="permissions-selected mb-3"></div>
                                    <div class="permissions-grid">
                                        @foreach ($permissionGroups as $group)
                                            @php
                                                $selectedPermissions = old('permissions', []);
                                                $moduleName = $group['module'] ?? null;
                                            @endphp
                                            <div class="permission-card permission-group">
                                                <div class="permission-header-wrap">
                                                    <label class="permission-header">
                                                        @if ($moduleName)
                                                            <input class="form-check-input permission-module-checkbox" type="checkbox"
                                                                name="permissions[]" value="{{ $moduleName }}"
                                                                id="create_perm_module_{{ $loop->index }}"
                                                                @checked(in_array($moduleName, $selectedPermissions, true))>
                                                        @endif
                                                        <span class="permission-module-label">{{ $permissionLabels[$moduleName] ?? $moduleName ?? 'Modulo' }}</span>
                                                    </label>
                                                    <button type="button" class="btn btn-link btn-sm permission-toggle-btn" data-target="create_perm_group_{{ $loop->index }}">Contraer</button>
                                                </div>
                                                <div class="permission-items" id="create_perm_group_{{ $loop->index }}">
                                                    @forelse ($group['permissions'] as $permission)
                                                        <label class="permission-item">
                                                            <input class="form-check-input permission-action-checkbox" type="checkbox"
                                                                name="permissions[]" value="{{ $permission }}"
                                                                @checked(in_array($permission, $selectedPermissions, true))>
                                                            <span class="permission-action-label">{{ $permissionLabels[$permission] ?? $permission }}</span>
                                                        </label>
                                                    @empty
                                                        <small class="text-muted">Sin permisos adicionales.</small>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="roleEditModal" tabindex="-1" role="dialog" aria-labelledby="roleEditLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="roleEditLabel">Editar rol</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="roleEditForm" action="{{ route('roles.update', ['role' => 'ROLE_ID']) }}"
                            data-action-template="{{ route('roles.update', ['role' => 'ROLE_ID']) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_modal" value="edit">
                            <input type="hidden" name="edit_role_id" id="edit_role_id" value="{{ old('edit_role_id') }}">
                            <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                                @if ($errors->any() && old('_modal') === 'edit')
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="role_edit_name">Nombre del rol</label>
                                    <input type="text" name="name" id="role_edit_name" class="form-control" value="{{ old('name') }}" required>
                                </div>

                                <div class="permissions-section">
                                    <div class="permissions-toolbar">
                                        <p class="permissions-title mb-0">Permisos</p>
                                        <div class="permissions-actions">
                                            <span class="badge badge-pill badge-primary" id="role_edit_perm_count">0</span>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="role_edit_perm_expand_all">Expandir todo</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="role_edit_perm_collapse_all">Contraer todo</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="role_edit_perm_select_all">Seleccionar todo</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="role_edit_perm_clear">Limpiar</button>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="text" class="form-control form-control-sm permission-search-input" id="role_edit_perm_search" placeholder="Buscar modulo o permiso...">
                                    </div>
                                    <div id="role_edit_perm_selected" class="permissions-selected mb-3"></div>
                                    <div class="permissions-grid">
                                        @foreach ($permissionGroups as $group)
                                            @php
                                                $moduleName = $group['module'] ?? null;
                                            @endphp
                                            <div class="permission-card permission-group">
                                                <div class="permission-header-wrap">
                                                    <label class="permission-header">
                                                        @if ($moduleName)
                                                            <input class="form-check-input permission-module-checkbox" type="checkbox"
                                                                name="permissions[]" value="{{ $moduleName }}"
                                                                id="edit_perm_module_{{ $loop->index }}">
                                                        @endif
                                                        <span class="permission-module-label">{{ $permissionLabels[$moduleName] ?? $moduleName ?? 'Modulo' }}</span>
                                                    </label>
                                                    <button type="button" class="btn btn-link btn-sm permission-toggle-btn" data-target="edit_perm_group_{{ $loop->index }}">Contraer</button>
                                                </div>
                                                <div class="permission-items" id="edit_perm_group_{{ $loop->index }}">
                                                    @forelse ($group['permissions'] as $permission)
                                                        <label class="permission-item">
                                                            <input class="form-check-input permission-action-checkbox" type="checkbox"
                                                                name="permissions[]" value="{{ $permission }}">
                                                            <span class="permission-action-label">{{ $permissionLabels[$permission] ?? $permission }}</span>
                                                        </label>
                                                    @empty
                                                        <small class="text-muted">Sin permisos adicionales.</small>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning"><i class="fa-solid fa-floppy-disk"></i> Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="roles-page-data" class="d-none" data-old-modal='@json(old('_modal'))'
                data-old-edit-role-id='@json(old('edit_role_id'))'
                data-old-edit-permissions='@json(old('permissions', []))'
                data-old-edit-name='@json(old('name'))'></div>

            <style>
                .permissions-section {
                    margin-top: 8px;
                }

                .permissions-toolbar {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 10px;
                    margin-bottom: 8px;
                    flex-wrap: wrap;
                }

                .permissions-actions {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    flex-wrap: wrap;
                }

                .permissions-title {
                    font-weight: 600;
                    margin: 0 0 8px 0;
                }

                .permissions-selected {
                    min-height: 44px;
                    border: 1px dashed #cbd5e1;
                    background: #f8fafc;
                    border-radius: 6px;
                    padding: 6px;
                }

                .permissions-empty {
                    margin: 0;
                    color: #64748b;
                    font-size: 13px;
                }

                .permission-chip {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    margin: 0 6px 6px 0;
                    border-radius: 999px;
                    padding: 3px 10px;
                    font-size: 12px;
                    border: 1px solid #dbeafe;
                    background: #eff6ff;
                    color: #1d4ed8;
                }

                .permission-chip button {
                    border: 0;
                    background: transparent;
                    color: #1e3a8a;
                    font-weight: 700;
                    line-height: 1;
                    cursor: pointer;
                    padding: 0;
                }

                .permission-highlight {
                    background: #fef08a;
                    color: inherit;
                    padding: 0 1px;
                    border-radius: 2px;
                }

                .permissions-grid {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                @media (min-width: 992px) {
                    .permissions-grid {
                        grid-template-columns: 1fr 1fr;
                    }
                }

                .permission-card {
                    border: 1px solid #d7dde5;
                    border-radius: 6px;
                    padding: 10px;
                    background: #fff;
                }

                .permission-header-wrap {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 8px;
                    margin-bottom: 8px;
                }

                .permission-header {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    margin: 0;
                    font-weight: 500;
                    line-height: 1.2;
                }

                .permission-card .form-check-input {
                    margin: 0;
                    position: relative;
                    top: -1px;
                }

                .permission-items {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 6px;
                    border-top: 1px solid #eef2f7;
                    padding-top: 8px;
                }

                .permission-items.is-collapsed {
                    display: none;
                }

                .permission-item {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    margin: 0;
                    padding: 4px 0;
                    border-bottom: 1px dashed #edf2f7;
                }

                .permission-item:last-child {
                    border-bottom: 0;
                }

                .permission-card small {
                    display: block;
                }
            </style>
        </div>
    </section>
@endsection
