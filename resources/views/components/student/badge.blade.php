{{-- Komponen Badge (Bahasa) - Anonymous component wrapper untuk <x-student.badge> --}}
@props(['variant' => 'default'])
@php
    $map = [
        'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
        'success' => 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-white',
        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-white',
        'danger' => 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-white',
        'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-white',
    ];
    $classes = $map[$variant] ?? $map['default'];
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {$classes}"]) }}>
    {{ $slot }}
</span>


