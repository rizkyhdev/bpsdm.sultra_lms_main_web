<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'AURA Dashboard') }}</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Bootstrap CSS (via CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- custom css -->
  <link href="{{ asset('./css/custom.css') }}" rel="stylesheet">
  <link href="{{ asset('./css/custom-style.css') }}" rel="stylesheet">
  <link href="{{ asset('./css/pelatihan.css') }}" rel="stylesheet">

  <!-- Font Awesome for Icons (via CDN) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="icon" href="{{ asset('image/LOGO AURA 1.png') }}" type="image/png">

</head>

<body class="bg-light d-flex flex-column min-vh-100">
  @include('layouts.partials.studentapp.header')

  <div class="page-container d-flex flex-fill fw-semibold">
    @hasSection('sidebar')
      @yield('sidebar')
    @else
      @include('layouts.partials.studentapp.sidebar')
    @endif
    <!-- Main Content -->
    <main class="flex-fill p-4">
      @yield('content')
    </main>
  </div>
  @include('layouts.partials.studentapp.footer')

  <!-- JS files -->
  <script src="{{ asset('./js/sidebar-toggle.js') }}"></script>
  <script src="{{ asset('./js/page-transition.js') }}"></script>
  <script src="{{ asset('./js/tabs.js') }}"></script>
</body>
</html>