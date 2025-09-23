@props([
    'title' => null,
    'actions' => null,
])

<section {{ $attributes->merge(['class' => 'rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950']) }}>
    @if($title || $actions)
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
            <h2 class="text-sm font-semibold">{{ $title }}</h2>
            <div class="flex items-center gap-2">{!! $actions !!}</div>
        </div>
    @endif
    <div class="p-4">
        {{ $slot }}
    </div>
</section>


