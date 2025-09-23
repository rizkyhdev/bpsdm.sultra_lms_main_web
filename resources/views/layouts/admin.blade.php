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
@include('layouts.partials.studentapp.header')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar kiri: gunakan sidebar studentapp untuk konsistensi -->
        <div class="col-md-2 d-none d-md-block admin-sidebar sidebar py-3">
            @include('layouts.partials.studentapp.sidebar')
        </div>

        <main role="main" class="col-md-10 ml-sm-auto px-0">
            <!-- Header global sudah disertakan di atas -->

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
    @include('layouts.partials.studentapp.footer')
</div>

<!-- JQuery dan Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>

@yield('scripts')

</body>
</html>


