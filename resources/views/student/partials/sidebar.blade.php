{{-- Sidebar Student (Bahasa): Navigasi utama --}}
<aside class="hidden lg:flex lg:flex-col w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700" aria-label="{{ __('Sidebar') }}">
    <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2" aria-label="{{ __('Home') }}">
            <img src="{{ asset('favicon.ico') }}" alt="{{ __('App Logo') }}" class="h-6 w-6" />
            <span class="font-semibold">{{ config('app.name', 'LMS') }}</span>
        </a>
    </div>
    <nav class="flex-1 px-3 py-4 overflow-y-auto" role="navigation">
        @php
            $link = function(string $route, string $label, string $icon) {
                $active = request()->routeIs($route);
                return [
                    'active' => $active,
                    'classes' => $active
                        ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white'
                        : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700',
                    'label' => $label,
                    'icon' => $icon,
                ];
            };
            $items = [
                ['route' => 'student.dashboard', 'label' => __('Dashboard'), 'icon' => 'home'],
                ['route' => 'student.courses.index', 'label' => __('Courses'), 'icon' => 'book-open'],
                ['route' => 'student.certificates.index', 'label' => __('Daftar Sertifikat'), 'icon' => 'book-open'],
                ['route' => 'student.progress.index', 'label' => __('Progress'), 'icon' => 'chart-bar'],
                ['route' => 'student.notifications.index', 'label' => __('Notifications'), 'icon' => 'bell'],
                ['route' => 'student.profile.show', 'label' => __('Profile'), 'icon' => 'user'],
                ['route' => 'student.settings.index', 'label' => __('Settings'), 'icon' => 'cog'],
            ];
        @endphp
        <ul class="space-y-1">
            @foreach($items as $item)
                @php($meta = $link($item['route'], $item['label'], $item['icon']))
                <li>
                    <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ $meta['classes'] }}">
                        @include('student.partials.svg.'.$item['icon'])
                        <span class="text-sm font-medium">{{ $meta['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</aside>

{{-- Mobile sidebar trigger --}}
<div class="lg:hidden">
    <button @click="open = !open" class="m-3 inline-flex items-center justify-center rounded-md p-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring" aria-controls="mobile-menu" :aria-expanded="open.toString()">
        <span class="sr-only">{{ __('Open main menu') }}</span>
        @include('student.partials.svg.bars-3')
    </button>
    <div x-show="open" x-trap.noscroll.inert="open" id="mobile-menu" class="fixed inset-0 z-40" @keydown.escape.window="open=false" aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
        <div class="absolute left-0 top-0 h-full w-72 bg-white dark:bg-gray-800 p-4 overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('favicon.ico') }}" alt="{{ __('App Logo') }}" class="h-6 w-6" />
                    <span class="font-semibold">{{ config('app.name', 'LMS') }}</span>
                </a>
                <button class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700" @click="open=false" aria-label="{{ __('Close menu') }}">
                    @include('student.partials.svg.x-mark')
                </button>
            </div>
            <ul class="space-y-1">
                @foreach($items as $item)
                    @php($meta = $link($item['route'], $item['label'], $item['icon']))
                    <li>
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ $meta['classes'] }}" @click="open=false">
                            @include('student.partials.svg.'.$item['icon'])
                            <span class="text-sm font-medium">{{ $meta['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    </div>


