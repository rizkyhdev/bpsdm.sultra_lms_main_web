{{-- Breadcrumbs (Bahasa): kirim array $crumbs = [['label' => 'Dashboard', 'route' => 'student.dashboard'], ...] --}}
@php /** @var array $crumbs */ @endphp
<nav class="mb-4" aria-label="{{ __('Breadcrumb') }}">
    <ol class="flex items-center text-sm text-gray-600 dark:text-gray-300 gap-2 flex-wrap">
        @foreach($crumbs as $index => $crumb)
            <li class="flex items-center gap-2">
                @if(isset($crumb['route']))
                    <a href="{{ route($crumb['route'], $crumb['params'] ?? []) }}" class="hover:underline">{{ __($crumb['label']) }}</a>
                @else
                    <span aria-current="page" class="font-medium">{{ __($crumb['label']) }}</span>
                @endif
                @if($index < count($crumbs) - 1)
                    <span class="text-gray-400">/</span>
                @endif
            </li>
        @endforeach
    </ol>
 </nav>


