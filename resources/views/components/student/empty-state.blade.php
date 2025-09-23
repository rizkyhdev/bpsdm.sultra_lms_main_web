{{-- Komponen Empty State (Bahasa) - Anonymous component wrapper untuk <x-student.empty-state> --}}
@props(['title' => __('Nothing here yet'), 'description' => null, 'action' => null])
<div class="text-center py-12">
    <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
        @include('student.partials.svg.face-frown')
    </div>
    <h3 class="text-base font-semibold">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $description }}</p>
    @endif
    @if($action)
        <div class="mt-4">{{ $action }}</div>
    @endif
    {{ $slot }}
</div>


