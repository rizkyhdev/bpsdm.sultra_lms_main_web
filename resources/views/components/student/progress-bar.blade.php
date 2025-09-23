{{-- Komponen Progress Bar (Bahasa) - Anonymous component wrapper untuk <x-student.progress-bar> --}}
@props(['value' => 0, 'label' => null])
@php
    $v = max(0, min(100, (int) $value));
@endphp
<div>
    <div class="flex items-center justify-between mb-1">
        @if($label)
            <span class="text-xs text-gray-600 dark:text-gray-300">{{ $label }}</span>
        @endif
        <span class="text-xs text-gray-600 dark:text-gray-300" aria-live="polite">{{ $v }}%</span>
    </div>
    <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full" role="progressbar" aria-valuenow="{{ $v }}" aria-valuemin="0" aria-valuemax="100">
        <div class="h-2 bg-indigo-600 rounded-full" style="width: {{ $v }}%"></div>
    </div>
</div>


