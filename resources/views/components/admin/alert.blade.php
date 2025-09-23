@props([
    'type' => 'info',
    'message' => null,
])

@php
    $color = [
        'info' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/30 dark:text-blue-200 dark:border-blue-900',
        'success' => 'bg-green-50 text-green-800 border-green-200 dark:bg-green-950/30 dark:text-green-200 dark:border-green-900',
        'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200 dark:bg-yellow-950/30 dark:text-yellow-200 dark:border-yellow-900',
        'error' => 'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/30 dark:text-red-200 dark:border-red-900',
    ][$type] ?? 'bg-gray-50 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700';
@endphp

<div role="status" {{ $attributes->merge(['class' => "rounded-md border p-3 text-sm " . $color]) }}>
    @if($message)
        <span>{{ $message }}</span>
    @else
        {{ $slot }}
    @endif
    @if ($errors->any())
        <ul class="mt-2 list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    @if (session('status-details'))
        <div class="mt-1 text-xs opacity-80">{!! session('status-details') !!}</div>
    @endif
</div>


