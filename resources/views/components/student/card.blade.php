{{-- Komponen Kartu (Bahasa) - Anonymous component wrapper untuk <x-student.card> --}}
@props(['title' => null, 'actions' => null])
<section {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700']) }}>
    @if($title || $actions)
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold">{{ $title }}</h2>
            @if($actions)
                <div>{{ $actions }}</div>
            @endif
        </div>
    @endif
    <div class="px-4 py-4">
        {{ $slot }}
    </div>
</section>


