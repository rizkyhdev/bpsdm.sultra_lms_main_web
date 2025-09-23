<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 text-sm text-gray-500 dark:text-gray-400 flex items-center justify-between">
    <div>
        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 dark:hover:text-gray-200">{{ __('Admin') }}</a>
    </div>
</div>


