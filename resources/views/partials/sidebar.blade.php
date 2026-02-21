<!-- Side Navbar -->
<nav class="side-navbar">
    <!-- Sidebar Header-->
    <div class="sidebar-header d-flex align-items-center">
        <div class="avatar">
            @if (Auth::user()->foto_perfil)
                <img src="{{ asset('storage/' . Auth::user()->foto_perfil) }}" alt="Foto de perfil" width="100"
                    height="100" class="img-fluid rounded-circle">
            @else
                <img src="{{ asset('img/default-avatar.png') }}" alt="Foto de perfil" width="100" height="100"
                    class="img-fluid rounded-circle">
            @endif
        </div>
        <div class="title">
            <h1 class="h5">{{ Auth::user()->name }} {{ Auth::user()->last_name }}</h1>
            @if (Auth::user()->group_id !== null)
                @if (Auth::user()->subgroup_id !== null)
                    <p>{{ Auth::user()->subgroup->descripcion }}</p>
                @else
                    <p>{{ Auth::user()->group->descripcion }}</p>
                @endif
            @endif
        </div>
    </div>
    <!-- Menus de navegacion del sidebar-->
    <span class="heading">NAVEGACION</span>
    <ul class="list-unstyled">

        <!-- Menu principal  -->
        <li class="{{ Request::routeIs('index') ? 'active' : '' }}"><a href="{{ route('index') }}"> <i
                    class="icon-home"></i>Home</a></li>

        <!-- Documentos -->
        <li
            class="{{ Request::routeIs('documents.*') ? 'active' : '' }} {{ Request::routeIs('blocks.*') ? 'active' : '' }}">
            <a href="#documentosdropdownDropdown" aria-expanded="false" data-toggle="collapse"> <i
                    class="icon-padnote"></i>Gestionar documentos</a>
            <ul id="documentosdropdownDropdown" class="collapse list-unstyled ">
                <li class="{{ Request::routeIs('documents.*') ? 'active' : '' }}">
                    <a href="{{ route('documents.index') }}">Documentos</a>
                </li>
                <li class="{{ Request::routeIs('blocks.*') ? 'active' : '' }}">
                    <a href="{{ route('blocks.index') }}">Bloques</a>
                </li>
            </ul>
        </li>
    </ul>

    <span class="heading">BANDEJA</span>
    <ul class="list-unstyled">
        <li class="{{ Request::routeIs('inbox.*') ? 'active' : '' }}"> <a href="{{ route('inbox.index') }}">
                <i class="icon-mail"></i>Bandeja de entrada</a></li>

        <!-- Almacenamiento de bloques -->
        <li class="{{ Request::routeIs('sections.*') ? 'active' : '' }}"> <a href="{{ route('sections.index') }}">
                <i class="icon-grid"></i>Almacenamiento</a></li>
    </ul>

    <!-- Menu de modulos extras -->
    <span class="heading">ADMINISTRACION</span>
    <ul class="list-unstyled">

        <!-- Gestion de usuarios -->
        <li class="{{ Request::routeIs('users.*') ? 'active' : '' }}"><a href="{{ route('users.index') }}">
                <i class="icon-user"></i>Usuarios</a></li>

        <!-- Gestion de roles y permisos -->
        <li class="{{ Request::routeIs('roles.*') || Request::routeIs('permissions.*') ? 'active' : '' }}">
            <a href="{{ route('roles.index') }}">
                <i class="fa-solid fa-user-shield"></i>Roles
            </a>
        </li>

        <!-- Gestion de tipos de docuemntos -->
        <li
            class="{{ Request::routeIs('document_types.*') ? 'active' : '' }} {{ Request::routeIs('campos.*') ? 'active' : '' }}">
            <a href="#camposdropdownDropdown" aria-expanded="false" data-toggle="collapse"> <i
                    class="fa-solid fa-file-invoice"></i> Inf. adicional de documentos</a>
            <ul id="camposdropdownDropdown" class="collapse list-unstyled ">
                <li
                    class="{{ Request::routeIs('document_types.*') ? 'active' : '' }} {{ Request::routeIs('groups.*') ? 'active' : '' }} {{ Request::routeIs('subgroups.*') ? 'active' : '' }}">
                    <a href="{{ route('document_types.index') }}">Tipos de documentos</a>
                </li>
                <li class="{{ Request::routeIs('campos.*') ? 'active' : '' }}"><a
                        href="{{ route('campos.index') }}">Campos</a></li>
            </ul>
        </li>

        <!-- Gestion de areas, grupos y subgrupos -->
        <li
            class="{{ Request::routeIs('areas.*') ? 'active' : '' }} {{ Request::routeIs('group_types.*') ? 'active' : '' }} {{ Request::routeIs('groups.*') ? 'active' : '' }} {{ Request::routeIs('subgroups.*') ? 'active' : '' }}">
            <a href="#exampledropdownDropdown" aria-expanded="false" data-toggle="collapse"> <i
                    class="icon-interface-windows"></i>Areas</a>
            <ul id="exampledropdownDropdown" class="collapse list-unstyled ">
                <li
                    class="{{ Request::routeIs('areas.*') ? 'active' : '' }} {{ Request::routeIs('groups.*') ? 'active' : '' }} {{ Request::routeIs('subgroups.*') ? 'active' : '' }}">
                    <a href="{{ route('areas.index') }}">Gestionar Areas</a>
                </li>
                <li class="{{ Request::routeIs('group_types.*') ? 'active' : '' }}"><a
                        href="{{ route('group_types.index') }}">Tipos de Grupos</a></li>
            </ul>
        </li>

        <!-- Registro de actividades del sistema -->
        <li class="{{ Request::routeIs('activity.logs') ? 'active' : '' }}"><a href="{{ route('activity.logs') }}">
                <i class="icon-screen"></i>Registro de actividades</a></li>
    </ul>
</nav>
