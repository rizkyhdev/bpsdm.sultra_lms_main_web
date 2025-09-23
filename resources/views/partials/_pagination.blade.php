{{-- Wrapper sederhana untuk pagination --}}
@if(method_exists($collection, 'links'))
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="small text-muted">
            {{-- Komentar: Opsional, tampilkan info total --}}
        </div>
        <div>
            {{ $collection->appends(request()->query())->links() }}
        </div>
    </div>
@endif

{{-- Pembungkus pagination dengan membawa query saat ini --}}
@if (isset($collection) && $collection instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            Menampilkan {{ $collection->firstItem() }} - {{ $collection->lastItem() }} dari {{ $collection->total() }} data
        </div>
        <div>
            {{ $collection->appends(request()->query())->links() }}
        </div>
    </div>
@endif


