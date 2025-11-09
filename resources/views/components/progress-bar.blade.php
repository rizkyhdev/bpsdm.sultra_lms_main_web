@props(['value'])

@php
    $percentage = min(100, max(0, (float) $value));
@endphp

<div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden" role="progressbar" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progress: {{ $percentage }}%">
    <div 
        class="h-full bg-indigo-600 transition-all duration-300 rounded-full"
        style="width: {{ $percentage }}%"
    ></div>
</div>

