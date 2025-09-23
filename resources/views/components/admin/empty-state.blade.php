@props([
    'icon' => null,
    'title' => null,
    'description' => null,
    'action' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-12']) }}>
    @if($icon)
        <div class="mx-auto mb-3 h-12 w-12 text-gray-400">{!! $icon !!}</div>
    @endif
    @if($title)
        <h3 class="text-sm font-semibold">{{ $title }}</h3>
    @endif
    @if($description)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
    @endif
    @if($action)
        <div class="mt-4">{!! $action !!}</div>
    @endif
</div>


