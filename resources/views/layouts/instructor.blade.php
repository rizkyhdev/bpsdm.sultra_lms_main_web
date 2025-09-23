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
@include('layouts.partials.studentapp.header')
<div class="d-flex">
    <div class="sidebar p-0" style="min-width: 260px;">
        @include('layouts.partials.studentapp.sidebar')
    </div>

    <div class="flex-grow-1">
        <div class="container-fluid content-wrapper">
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
@include('layouts.partials.studentapp.footer')

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>


