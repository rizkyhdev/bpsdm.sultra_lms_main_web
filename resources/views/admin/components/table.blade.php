@props([
    'headers' => [], // [['key' => 'name', 'label' => 'Name', 'sortable' => true]]
])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-900/40">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @if(($header['sortable'] ?? false) && isset($header['key']))
                            @php($qs = array_merge(request()->all(), ['sort' => $header['key']]))
                            <a href="?{{ http_build_query($qs) }}" class="hover:underline">{{ $header['label'] }}</a>
                        @else
                            {{ $header['label'] }}
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-950 divide-y divide-gray-100 dark:divide-gray-900">
            {{ $slot }}
        </tbody>
    </table>
</div>


