{{--
    Layout utama Student
    Catatan (Bahasa):
    - Gunakan Tailwind + Vite
    - Sidebar responsif, Topnav, slot konten, dan flash messages
--}}
@php
    /** @var array|null $settings */
    $theme = $settings->theme ?? (cookie('theme', 'light'));
    $isDark = $theme === 'dark';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $isDark ? 'dark' : '' }} h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Sobat AURA') }}</title>
        @vite(['resources/css/app.css','resources/js/app.js'])
    </head>
    <body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div x-data="{ open: false }" class="min-h-screen flex overflow-hidden">
            {{-- Sidebar --}}
            @include('student.partials.sidebar')

            <div class="flex-1 flex flex-col min-w-0">
                {{-- Top Navigation --}}
                @include('student.partials.topnav')

                {{-- Flash messages --}}
                <main id="main" tabindex="-1" class="focus:outline-none">
                    <div class="container mx-auto max-w-7xl px-4 py-6">
                        @if(session('status'))
                            <x-student::alert type="success" :message="session('status')" />
                        @endif
                        @if ($errors->any())
                            <x-student::alert type="error" :message="__('Please check the form for errors.')" />
                        @endif
                        @yield('content')
                    </div>
                </main>

                @include('student.partials.footer')
            </div>
        </div>
    </body>
</html>


