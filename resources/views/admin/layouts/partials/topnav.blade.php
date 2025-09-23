<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 group">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded bg-indigo-600 text-white">B</span>
            <span class="font-semibold group-hover:text-indigo-600">{{ config('app.name', __('Admin')) }}</span>
        </a>
        <form action="{{ route('admin.users.index') }}" method="GET" class="hidden md:block">
            <label for="top-search" class="sr-only">{{ __('Search') }}</label>
            <div class="relative">
                <input id="top-search" name="q" value="{{ request('q') }}" class="pl-9 pr-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="{{ __('Search...') }}">
                <div class="absolute inset-y-0 left-0 flex items-center pl-2 text-gray-400">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8 4a4 4 0 102.546 7.122l3.666 3.666a1 1 0 001.415-1.415l-3.666-3.666A4 4 0 008 4zM6 8a2 2 0 114 0 2 2 0 01-4 0z" clip-rule="evenodd"/></svg>
                </div>
            </div>
        </form>
    </div>
    <div class="flex items-center gap-3">
        <button type="button" x-data x-on:click="document.documentElement.classList.toggle('dark')" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-700 text-sm hover:bg-gray-50 dark:hover:bg-gray-800" aria-label="{{ __('Toggle dark mode') }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            <span class="hidden sm:inline">{{ __('Theme') }}</span>
        </button>
        <div class="relative">
            <button class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-700 text-sm" aria-haspopup="menu" aria-expanded="false">{{ auth()->user()->nama ?? __('User') }}</button>
        </div>
    </div>
</div>


