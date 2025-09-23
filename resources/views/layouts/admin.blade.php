<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin - BPSDM Sultra')</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css">
    <!-- Ikon Font Awesome untuk tombol aksi -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Asset aplikasi -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* Tema netral sederhana untuk admin */
        body { background-color: #f5f7fb; }
        .admin-sidebar { min-height: 100vh; background: #1f2937; color: #e5e7eb; }
        .admin-sidebar a { color: #e5e7eb; }
        .admin-sidebar .active, .admin-sidebar a:hover { color: #ffffff; text-decoration: none; }
        .content-wrapper { padding: 20px; }
        .breadcrumb { background: transparent; margin-bottom: 0; }
        .table thead th { white-space: nowrap; }
        .form-inline .form-control { width: auto; }
        .nav-link { white-space: nowrap; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar kiri -->
        <nav class="col-md-2 d-none d-md-block admin-sidebar sidebar py-3">
            <div class="sidebar-sticky">
                <h5 class="px-3 mb-3">BPSDM Sultra</h5>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link @if(request()->is('admin')) active @endif" href="{{ route('admin.dashboard') }}"><i class="fas fa-home mr-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->is('admin/users*')) active @endif" href="{{ route('admin.users.index') }}"><i class="fas fa-users mr-2"></i> Pengguna</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->is('admin/courses*')) active @endif" href="{{ route('admin.courses.index') }}"><i class="fas fa-book-open mr-2"></i> Kursus</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->is('admin/enrollments*')) active @endif" href="{{ route('admin.enrollments.index') }}"><i class="fas fa-user-plus mr-2"></i> Pendaftaran</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->is('admin/certificates*')) active @endif" href="{{ route('admin.certificates.index') }}"><i class="fas fa-certificate mr-2"></i> Sertifikat</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->is('admin/reports*')) active @endif" href="{{ route('admin.reports.dashboard') }}"><i class="fas fa-chart-bar mr-2"></i> Laporan</a></li>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-10 ml-sm-auto px-0">
            <!-- Navbar atas -->
            <nav class="navbar navbar-expand navbar-light bg-white border-bottom">
                <button class="navbar-toggler d-md-none" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Pencarian global -->
                <form class="form-inline mr-auto" action="#" method="GET">
                    <!-- Catatan: Controller dapat meng-handle pencarian global bila diperlukan -->
                    <input class="form-control mr-sm-2" type="search" name="q" placeholder="Cari..." aria-label="Search" value="{{ request('q') }}">
                    <button class="btn btn-outline-secondary my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
                </form>
                <!-- Menu user -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-circle mr-1"></i> {{ auth()->user()->nama ?? 'Admin' }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userMenu">
                            <a class="dropdown-item" href="{{ route('home') }}">Beranda</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Keluar</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="content-wrapper">
                <!-- Breadcrumbs -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-1">@yield('title', 'Dashboard')</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb p-0">
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                    </div>
                    <div>
                        @yield('header-actions')
                    </div>
                </div>

                @include('partials._flash')
                @include('partials._errors')

                @yield('content')
            </div>
        </main>
    </div>
</div>

<!-- JQuery dan Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>

@yield('scripts')

</body>
</html>


