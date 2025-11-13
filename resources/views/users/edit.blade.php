@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
    @vite('resources/js/users/area_groupType_group_subgroup_selector.js')
    <header class="page-header">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <h2 class="no-margin-bottom">Editar usuario</h2>
        </div>
    </header>

    <div class="breadcrumb-holder container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ul>
    </div>

    <section class="dashboard-counts no-padding-bottom">
        <div class="container-fluid">

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="fa-solid fa-circle-check mr-2" aria-hidden="true"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
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

            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left-long" aria-hidden="true"></i> Volver
            </a>

            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-lg-4 mb-5">
                        <div class="card bg-white has-shadow h-100">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="col">
                                    <h5 class="mb-3"><i class="fa-solid fa-image-portrait mr-2"></i>Foto de perfil</h5>
                                    <div class="text-center">
                                        @php
                                            $avatar = $user->foto_perfil
                                                ? asset('storage/' . $user->foto_perfil)
                                                : asset('img/default-avatar.png');
                                        @endphp
                                        <img id="preview" src="{{ $avatar }}" alt="Preview"
                                            class="rounded-circle mb-3" width="180" height="180"
                                            style="object-fit: cover;">
                                        <div class="custom-file">
                                            <input class="custom-file-input @error('foto_perfil') is-invalid @enderror"
                                                type="file" id="foto_perfil" name="foto_perfil" accept="image/*"
                                                onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0])">
                                            <label class="custom-file-label" for="foto_perfil">Cambiar imagen</label>
                                            @error('foto_perfil')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted d-block mt-2">PNG/JPG hasta 2MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 mb-4">
                        <div class="card bg-white has-shadow">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="fa-solid fa-id-card mr-2"></i>Datos personales</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Nombres</label>
                                        <input id="name" name="name" type="text"
                                            value="{{ old('name', $user->name) }}"
                                            class="form-control @error('name') is-invalid @enderror">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="last_name">Apellidos</label>
                                        <input id="last_name" name="last_name" type="text"
                                            value="{{ old('last_name', $user->last_name) }}"
                                            class="form-control @error('last_name') is-invalid @enderror">
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="dni">DNI</label>
                                        <input id="dni" name="dni" type="text"
                                            value="{{ old('dni', $user->dni) }}"
                                            class="form-control @error('dni') is-invalid @enderror">
                                        @error('dni')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <h5 class="mb-3 mt-4"><i class="fa-solid fa-user-gear mr-2"></i>Cuenta</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="user_name">Usuario</label>
                                        <input id="user_name" name="user_name" type="text"
                                            value="{{ old('user_name', $user->user_name) }}"
                                            class="form-control @error('user_name') is-invalid @enderror">
                                        @error('user_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label for="email">Correo</label>
                                        <input id="email" name="email" type="email"
                                            value="{{ old('email', $user->email) }}"
                                            class="form-control @error('email') is-invalid @enderror">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="user_type_id">Tipo de usuario</label>
                                        <select id="user_type_id" name="user_type_id"
                                            class="form-control @error('user_type_id') is-invalid @enderror">
                                            <option value="">Seleccione</option>
                                            @foreach ($userTypes ?? [] as $type)
                                                <option value="{{ $type->id }}" @selected(old('user_type_id', $user->user_type_id) == $type->id)>
                                                    {{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('user_type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="password">Nueva contraseña <small
                                                class="text-muted">(opcional)</small></label>
                                        <input id="password" name="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Dejar en blanco para mantener la actual">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="password_confirmation">Confirmar contraseña</label>
                                        <input id="password_confirmation" name="password_confirmation" type="password"
                                            class="form-control">
                                    </div>
                                </div>

                                @php
                                    $userAreaId = optional(optional(optional($user->group)->areaGroupType)->area)->id;
                                @endphp

                                <h5 class="mb-3 mt-4"><i class="fa-solid fa-sitemap mr-2"></i>Organización</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="area">Área</label>
                                        <select id="area" name="area" class="form-control">
                                            <option value="">Seleccione</option>
                                            @foreach ($areas ?? [] as $area)
                                                <option value="{{ $area->id }}" @selected(old('area', $userAreaId) == $area->id)>
                                                    {{ $area->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="groupType" class="form-control-label">Tipo de Grupo</label>
                                        <select class="form-control" id="groupType" name="groupType" required>
                                            <option value="">-- Sin Tipo de Grupo --</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="group">Grupo</label>
                                        <select id="group" name="group" class="form-control">
                                            <option value="">-- Sin Grupo --</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="subgroup">Subgrupo</label>
                                        <select id="subgroup" name="subgroup" class="form-control">
                                            <option value="">-- Sin Subgrupo --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                                        Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </section>
    <script>
        window.areas = @json($areas);
        window.user = @json($user);

        window.selectedAreaId = document.getElementById('area');
        window.selectedGroupType = document.getElementById('groupType');
        window.selectedGroupId = document.getElementById('group');
        window.selectedSubgroupId = document.getElementById('subgroup');
    </script>
@endsection
