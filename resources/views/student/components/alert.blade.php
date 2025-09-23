{{-- Komponen Alert (Bahasa) --}}
@props(['type' => 'info', 'message' => null])
@php
    $colors = [
        'success' => 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:text-green-200 dark:border-green-800',
        'error' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:text-red-200 dark:border-red-800',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200 dark:border-yellow-800',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:text-blue-200 dark:border-blue-800',
    ];
    $color = $colors[$type] ?? $colors['info'];
@endphp
<div role="alert" {{ $attributes->merge(['class' => "rounded-md border px-3 py-2 text-sm {$color}"]) }}>
    @if($message)
        <p>{{ $message }}</p>
    @else
        {{ $slot }}
    @endif
</div>


