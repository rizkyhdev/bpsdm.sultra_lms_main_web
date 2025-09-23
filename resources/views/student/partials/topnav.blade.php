{{-- Top Navigation Student (Bahasa): Pencarian, notifikasi, user menu --}}
<header class="sticky top-0 z-30 bg-white/80 dark:bg-gray-900/80 backdrop-blur border-b border-gray-200 dark:border-gray-700">
    <div class="h-16 flex items-center justify-between px-4">
        <div class="flex items-center gap-3">
            <button class="lg:hidden p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800" @click="open = !open" aria-label="{{ __('Open sidebar') }}">
                @include('student.partials.svg.bars-3')
            </button>
            <a href="{{ route('student.dashboard') }}" class="hidden sm:flex items-center gap-2" aria-label="{{ __('Home') }}">
                <img src="{{ asset('favicon.ico') }}" alt="{{ __('App Logo') }}" class="h-6 w-6" />
                <span class="font-semibold">{{ config('app.name', 'LMS') }}</span>
            </a>
        </div>

        <div class="flex-1 max-w-xl mx-4">
            <form action="#" method="GET" role="search" aria-label="{{ __('Search') }}">
                <label for="topnav-search" class="sr-only">{{ __('Search') }}</label>
                <input id="topnav-search" name="q" type="search" class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="{{ __('Search courses, modules...') }}" />
            </form>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('student.notifications.index') }}" class="relative p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="{{ __('Notifications') }}">
                @include('student.partials.svg.bell')
                @php($unread = session('student_unread_notifications', 0))
                @if($unread > 0)
                    <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center rounded-full bg-red-600 text-white text-[10px] h-4 min-w-[16px] px-1" aria-label="{{ trans_choice(':count unread notifications', $unread, ['count'=>$unread]) }}">{{ $unread }}</span>
                @endif
            </a>

            <div x-data="{ openUser: false }" class="relative">
                <button @click="openUser = !openUser" @keydown.escape.window="openUser=false" class="flex items-center gap-2 p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800" aria-haspopup="menu" :aria-expanded="openUser.toString()">
                    <img src="{{ auth()->user()->avatar_url ?? asset('image/user.png') }}" class="h-8 w-8 rounded-full" alt="{{ __('Avatar') }}">
                    <span class="hidden md:inline text-sm">{{ auth()->user()->name ?? __('Student') }}</span>
                    @include('student.partials.svg.chevron-down')
                </button>
                <div x-cloak x-show="openUser" x-transition @click.outside="openUser=false" class="absolute right-0 mt-2 w-48 rounded-md bg-white dark:bg-gray-800 shadow border border-gray-200 dark:border-gray-700 py-1" role="menu" aria-label="{{ __('User menu') }}">
                    <a href="{{ route('student.profile.show') }}" class="block px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">{{ __('Profile') }}</a>
                    <a href="{{ route('student.settings.index') }}" class="block px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">{{ __('Settings') }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-200 dark:border-gray-700 mt-1 pt-1">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">{{ __('Log Out') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>


