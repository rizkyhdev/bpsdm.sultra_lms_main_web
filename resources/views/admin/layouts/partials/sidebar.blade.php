@php
    $nav = [
        ['label' => __('Dashboard'), 'route' => 'admin.dashboard', 'icon' => 'home', 'active' => request()->routeIs('admin.dashboard')],
        ['label' => __('Users'), 'route' => 'admin.users.index', 'icon' => 'users', 'active' => request()->routeIs('admin.users.*')],
        ['label' => __('Courses'), 'route' => 'admin.courses.index', 'icon' => 'book-open', 'active' => request()->routeIs('admin.courses.*')],
        ['label' => __('Reports'), 'route' => 'admin.reports.dashboard', 'icon' => 'chart', 'active' => request()->routeIs('admin.reports.*')],
        ['label' => __('Logs'), 'route' => 'admin.reports.dashboard', 'icon' => 'document', 'active' => false],
        ['label' => __('Notifications'), 'route' => 'admin.users.index', 'icon' => 'bell', 'active' => false],
        ['label' => __('Settings'), 'route' => 'admin.dashboard', 'icon' => 'cog', 'active' => false],
    ];
@endphp
<div class="h-[calc(100vh-4rem)] overflow-y-auto p-3">
    <ul class="space-y-1">
        @foreach($nav as $item)
            <li>
                <a href="{{ route($item['route']) }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-md text-sm',
                    'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' => $item['active'],
                    'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' => !$item['active'],
                ]) aria-current="{{ $item['active'] ? 'page' : 'false' }}">
                    <span class="inline-flex h-5 w-5 items-center justify-center">
                        {{-- Simple icon placeholders; replace with your icon component if any --}}
                        <span class="block h-1.5 w-1.5 rounded-full bg-current"></span>
                    </span>
                    <span>{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>


