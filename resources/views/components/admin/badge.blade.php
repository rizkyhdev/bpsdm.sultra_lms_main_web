@props([
    'color' => 'gray',
])

@php
    $classes = [
        'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100',
        'indigo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
        'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    ][$color] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ' . $classes]) }}>
    {{ $slot }}
    </span>


