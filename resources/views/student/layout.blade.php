<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Dashboard') - {{ config('app.name', 'LMS') }}</title>
    
    @vite(['resources/js/student.js'])
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <!-- Topbar Navigation -->
    <nav class="bg-white border-b border-slate-200 shadow-sm" role="navigation" aria-label="Main navigation">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.dashboard') }}" class="text-xl font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                        {{ config('app.name', 'LMS') }}
                    </a>
                    {{-- Mobile menu button --}}
                    <button type="button" class="md:hidden p-2 rounded-md text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" x-data @click="$dispatch('toggle-mobile-menu')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('student.dashboard') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('student.dashboard') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' }}"
                       aria-current="{{ request()->routeIs('student.dashboard') ? 'page' : null }}">
                        Dashboard
                    </a>
                    <a href="{{ route('student.pelatihan') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('student.pelatihan') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' }}"
                       aria-current="{{ request()->routeIs('student.pelatihan') ? 'page' : null }}">
                        Daftar Pelatihan
                    </a>
                    <a href="{{ route('student.wishlist') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('student.wishlist') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' }}"
                       aria-current="{{ request()->routeIs('student.wishlist') ? 'page' : null }}">
                        Daftar Keinginan
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="hidden sm:inline text-sm text-slate-600 font-medium">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-slate-600 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded px-3 py-2 hover:bg-slate-100 transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>

