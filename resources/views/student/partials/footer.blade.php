{{-- Footer sederhana (Bahasa) --}}
<footer class="border-t border-gray-200 dark:border-gray-700">
    <div class="container mx-auto max-w-7xl px-4 py-4 text-sm text-gray-600 dark:text-gray-300 flex items-center justify-between">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'LMS') }}. {{ __('All rights reserved.') }}</p>
        <nav class="flex items-center gap-4" aria-label="{{ __('Footer') }}">
            <a href="#" class="hover:underline">{{ __('Help') }}</a>
            <a href="#" class="hover:underline">{{ __('Privacy') }}</a>
            <a href="#" class="hover:underline">{{ __('Terms') }}</a>
        </nav>
    </div>
</footer>


