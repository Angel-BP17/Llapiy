<!-- Main Navbar-->
<header class="header">
    <nav class="navbar">
        <!-- Search Box
        <div class="search-box">
            <button class="dismiss"><i class="icon-close"></i></button>
            <form id="searchForm" action="#" role="search">
                <input type="search" placeholder="What are you looking for..." class="form-control">
            </form>
        </div>-->
        <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
                <!-- Navbar Header-->
                <div class="navbar-header">
                    <!-- Navbar Brand --><a href="{{ route('index') }}" class="navbar-brand d-none d-sm-inline-block">
                        <div class="brand-text d-none d-lg-inline-block"><strong>Llapiy</strong></div>
                        <div class="brand-text d-none d-sm-inline-block d-lg-none"><strong>LLPY</strong></div>
                    </a>
                    <!-- Toggle Button--><a id="toggle-btn" href="#"
                        class="menu-btn active"><span></span><span></span><span></span></a>
                </div>
                <!-- Navbar Menu -->
                <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                    <!-- Search
                    <li class="nav-item d-flex align-items-center"><a id="search" href="#"><i
                                class="icon-search"></i></a>
                    </li>-->
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a id="notifications" rel="nofollow" data-target="#" href="#" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" class="nav-link">
                            <i class="fa fa-bell-o"></i>
                            @if (Auth::user()->unreadNotifications->count() > 0)
                                <span
                                    class="badge bg-red badge-corner">{{ Auth::user()->unreadNotifications->count() }}</span>
                            @endif
                        </a>
                        <ul aria-labelledby="notifications" class="dropdown-menu"
                            style="max-height: 300px; overflow-y: auto;">
                            @forelse (Auth::user()->notifications->take(10) as $notification)
                                <li>
                                    <a rel="nofollow" href="{{ route('notification.show', $notification->id) }}"
                                        class="dropdown-item {{ $notification->read_at ? '' : 'font-weight-bold' }}">
                                        <div class="notification">
                                            <div class="notification-content">
                                                <i
                                                    class="fa fa-file {{ $notification->read_at ? 'bg-light' : 'bg-green' }}"></i>
                                                {{ $notification->data['message'] }}
                                            </div>
                                            <div class="notification-time">
                                                <small>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="dropdown-item text-center">No hay notificaciones nuevas</li>
                            @endforelse
                            <li><a rel="nofollow" href="{{ route('notifications.index') }}"
                                    class="dropdown-item all-notifications text-center">
                                    <strong>Ver todas las notificaciones</strong>
                                </a></li>
                        </ul>
                    </li>

                    <!-- Messages
                    <li class="nav-item dropdown"> <a id="messages" rel="nofollow" data-target="#" href="#"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link"><i
                                class="fa fa-envelope-o"></i><span class="badge bg-orange badge-corner">10</span></a>
                        <ul aria-labelledby="notifications" class="dropdown-menu">
                            <li><a rel="nofollow" href="#" class="dropdown-item d-flex">
                                    <div class="msg-profile"> <img src="/Proyecto llapiy/view/img/avatar-1.jpg"
                                            alt="..." class="img-fluid rounded-circle"></div>
                                    <div class="msg-body">
                                        <h3 class="h5">Jason Doe</h3><span>Sent You Message</span>
                                    </div>
                                </a></li>
                            <li><a rel="nofollow" href="#" class="dropdown-item d-flex">
                                    <div class="msg-profile"> <img src="/Proyecto llapiy/view/img/avatar-2.jpg"
                                            alt="..." class="img-fluid rounded-circle"></div>
                                    <div class="msg-body">
                                        <h3 class="h5">Frank Williams</h3><span>Sent You Message</span>
                                    </div>
                                </a></li>
                            <li><a rel="nofollow" href="#" class="dropdown-item d-flex">
                                    <div class="msg-profile"> <img src="/Proyecto llapiy/view/img/avatar-3.jpg"
                                            alt="..." class="img-fluid rounded-circle"></div>
                                    <div class="msg-body">
                                        <h3 class="h5">Ashley Wood</h3><span>Sent You Message</span>
                                    </div>
                                </a></li>
                            <li><a rel="nofollow" href="#" class="dropdown-item all-notifications text-center">
                                    <strong>Read all
                                        messages </strong></a></li>
                        </ul>
                    </li>
                    -->
                    <!-- Languages dropdown
                    <li class="nav-item dropdown"><a id="languages" rel="nofollow" data-target="#" href="#"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            class="nav-link language dropdown-toggle"><img src="{{ asset('img/flags/16/GB.png') }}"
                                alt="English"><span class="d-none d-sm-inline-block">English</span></a>
                        <ul aria-labelledby="languages" class="dropdown-menu">
                            <li><a rel="nofollow" href="#" class="dropdown-item"> <img
                                        src="{{ asset('img/flags/16/DE.png') }}" alt="English"
                                        class="mr-2">German</a></li>
                            <li><a rel="nofollow" href="#" class="dropdown-item"> <img
                                        src="/Proyecto llapiy/view/img/flags/16/FR.png" alt="English"
                                        class="mr-2">French </a></li>
                        </ul>
                    </li>
                    -->
                    <!-- Logout    -->
                    <li class="nav-item"><a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"class="nav-link logout">
                            <span class="d-none d-sm-inline">Logout</span><i class="fa fa-sign-out"></i></a>
                    </li>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </ul>
            </div>
        </div>
    </nav>
</header>
