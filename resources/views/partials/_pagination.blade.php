{{-- Global pagination wrapper (Bootstrap-friendly, used across LMS admin & student views) --}}
@if (isset($collection) && $collection instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            Menampilkan {{ $collection->firstItem() }} - {{ $collection->lastItem() }} dari {{ $collection->total() }} data
        </div>
        <div>
            {{-- Use Bootstrap pagination template and preserve current query params --}}
            {{ $collection->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endif


