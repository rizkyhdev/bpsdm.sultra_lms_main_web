@props(['collection'])

<div class="mt-4">
    {{ $collection->onEachSide(1)->links() }}
</div>


