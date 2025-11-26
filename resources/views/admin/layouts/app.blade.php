@php
    /**
     * Admin base layout
     * - Uses Vite: resources/css/app.css, resources/js/app.js
     * - Provides semantic landmarks: header, nav, main, footer
     * - Dark mode via class="dark" on <html> (handled by app.js toggle)
     * - Exposes slots: title, breadcrumb, header-actions, content
     */
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trim($__env->yieldContent('title', __('Admin'))) }} · {{ config('app.name') }}</title>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
<div class="min-h-full">
    <header role="banner" aria-label="{{ __('Top navigation') }}" class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-gray-200 dark:bg-gray-900/70 dark:border-gray-800">
        @include('admin.layouts.partials.topnav')
    </header>

    <div class="flex">
        <nav role="navigation" aria-label="{{ __('Sidebar') }}" class="w-64 shrink-0 hidden lg:block bg-white border-r border-gray-200 dark:bg-gray-950 dark:border-gray-800">
            @include('admin.layouts.partials.sidebar')
        </nav>

        <main role="main" class="flex-1">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-xl font-semibold">@yield('title', __('Admin'))</h1>
                        @hasSection('breadcrumb')
                            <nav class="mt-2" aria-label="{{ __('Breadcrumb') }}">
                                <ol class="flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @yield('header-actions')
                    </div>
                </div>

                @if (session('status'))
                    <x-admin.alert type="success" :message="session('status')" />
                @endif
                @if (session('error'))
                    <x-admin.alert type="error" :message="session('error')" />
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <footer role="contentinfo" class="border-t border-gray-200 dark:border-gray-800 bg-white/60 dark:bg-gray-950/60">
        @include('admin.layouts.partials.footer')
    </footer>
</div>

@stack('scripts')
</body>
</html>


