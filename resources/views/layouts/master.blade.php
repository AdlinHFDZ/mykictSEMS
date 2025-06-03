<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MYKICT</title>

    <link rel="shortcut icon" href="{{ asset('assets/img/logokict.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/flags/flags.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stack('styles')
</head>

<body>
    <div class="main-wrapper">
        <div class="header">

            <div class="header-left">
                <a href="{{ route('dashboard') }}" class="logo">
                    <img src="{{ asset('assets/img/LOGO-KICT.png') }}" alt="Logo">
                </a>
                <a href="{{ route('dashboard') }}" class="logo logo-small">
                    <img src="{{ asset('assets/img/LOGO-KICT.png') }}" alt="Logo" width="30" height="30">
                </a>
            </div>

            <div class="menu-toggle">
                <a href="javascript:void(0);" id="toggle_btn">
                    <i class="fas fa-bars"></i>
                </a>
            </div>

            <div class="top-nav-search">
                <form>
                    <input type="text" class="form-control" placeholder="Search here">
                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <a class="mobile_btn" id="mobile_btn">
                <i class="fas fa-bars"></i>
            </a>

            <ul class="nav user-menu">
                <li class="nav-item zoom-screen me-2">
                    <a href="#" class="nav-link header-nav-list win-maximize">
                        <img src="assets/img/icons/header-icon-04.svg" alt="">
                    </a>
                </li>

                @auth
                <li class="nav-item dropdown has-arrow new-user-menus">
                    <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="{{ asset('assets/img/profiles/avatar-01.jpg') }}" width="31"
                                alt="{{ Auth::user()->name }}">
                            <div class="user-text">
                                <h6>{{ Auth::user()->name }}</h6>
                                <p class="text-muted mb-0">{{ Auth::user()->role->name }}</p>
                            </div>
                        </span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('update.profile') }}">My Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </div>
                </li>
                @endauth

                @guest
                <li class="nav-item">
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                </li>
                @endguest
            </ul>
        </div>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">
                            <span>Main Menu</span>
                        </li>
                        @auth
                            @if (Auth::user()->role_id == '1')
                                <li class="submenu active">
                                    <a href="#"><i class="feather-grid"></i> <span> Dashboard</span> <span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="dashboard">Welcome Dashboard</a></li>
                                        <li><a href="admin-dashboard">Admin Dashboard</a></li>
                                        <!--<li><a href="teacher-dashboard">Teacher Dashboard</a></li>-->
                                        <li><a href="student-dashboard">Student Dashboard</a></li>
                                    </ul>
                                </li>
                            @endif

                            @if (Auth::user()->role_id == '1' || Auth::user()->role_id == '5')
                                <li class="submenu">
                                    <a href="#"><i class="fas fa-chalkboard-teacher"></i> <span> Academicians</span> <span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="teachers.html">Monthly Achievement</a></li>
                                        <li><a href="add-teacher.html">Teacher Add</a></li>
                                        <li><a href="edit-teacher.html">Teacher Edit</a></li>
                                    </ul>
                                </li>
                            @endif

                            @if (Auth::user()->role_id == '1' || Auth::user()->role_id == '5')
                                <li class="submenu">
                                    <a href="#"><i class="fas fa-chalkboard-teacher"></i> <span> SEMS</span> <span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="{{ route('SEMS.dashboard') }}">SEMS</a></li>
                                    </ul>
                                </li>
                            @endif

                            @if (Auth::user()->role_id == '1' || Auth::user()->role_id == '6')
                                <li class="submenu">
                                    <a href="#"><i class="fas fa-graduation-cap"></i> <span>Smart Study Planner</span> <span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="admin-welcome">Administrator</a></li>
                                        <li><a href="SSP-welcome">Student</a></li>
                                    </ul>
                                </li>
                            @endif
                        @endauth
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-wrapper">
            @yield('content')
            <footer>
                <p>Copyright © 2025 MYKICT.</p>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>
