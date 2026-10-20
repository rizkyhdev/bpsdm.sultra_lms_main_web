@php
    $statusClass = match ($status) {
        'Selesai' => 'bg-success',
        'Sedang Mengerjakan' => 'bg-warning text-dark',
        default => 'bg-secondary',
    };
@endphp
<span class="badge {{ $statusClass }}">{{ $status }}</span>
