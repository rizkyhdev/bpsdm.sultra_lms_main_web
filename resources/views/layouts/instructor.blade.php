@php
    // Layout untuk role Instructor. Mengikuti pola sederhana seperti layouts.studentapp
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Instructor') - BPSDM Sultra LMS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
    <style>
        /* Sidebar minimal */
        .sidebar { min-height: 100vh; background: #f8f9fa; border-right: 1px solid #e9ecef; }
        .sidebar .nav-link { color: #333; }
        .sidebar .nav-link.active { font-weight: 600; }
        .content-wrapper { padding-top: 1rem; }
        .topbar-search { max-width: 420px; width: 100%; }
    </style>
    <!-- Komentar: Layout ini mengasumsikan Bootstrap 4 dan file public/css/app.css tersedia -->
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar p-3">
        <h5 class="mb-4">Instructor</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('instructor.dashboard') }}">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('instructor.courses.index') }}">Courses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('instructor.reports.course') }}">Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Help</a>
            </li>
        </ul>
    </nav>

    <!-- Main -->
    <div class="flex-grow-1">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white border-bottom px-3">
            <form class="form-inline mr-auto w-100" action="{{ route('instructor.courses.index') }}" method="get">
                <!-- Komentar: Kotak pencarian global sederhana, diarahkan ke daftar courses -->
                <input class="form-control topbar-search" type="search" name="q" value="{{ request('q') }}" placeholder="Cari...">
            </form>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ auth()->user()->name ?? 'User' }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="{{ route('instructor.dashboard') }}">Profil</a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="post" class="px-3">
                            @csrf
                            <button type="submit" class="btn btn-link p-0">Logout</button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        <div class="container-fluid content-wrapper">
            <!-- Breadcrumb -->
            <div class="mb-3">
                @yield('breadcrumb')
            </div>

            @include('partials._flash')
            @include('partials._errors')

            <h4 class="mb-3">@yield('title')</h4>
            @yield('content')
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>


