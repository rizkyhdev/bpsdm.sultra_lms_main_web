{{--
    @phpdoc
    Variabel:
    - $notifications: LengthAwarePaginator|Collection berisi {id,title,body,read_at,created_at}
--}}
@extends('student.layouts.app')

@section('title', __('Notifications'))

@section('content')
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'route' => 'student.dashboard'],
        ['label' => 'Notifications'],
    ]])

    <x-student::card>
        @if(($notifications ?? collect())->isEmpty())
            <x-student::empty-state :title="__('No notifications')" :description="__('You are all caught up!')" />
        @else
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($notifications as $n)
                    <li class="py-3 flex items-start justify-between gap-4 {{ $n->read_at ? '' : 'bg-indigo-50/50 dark:bg-indigo-900/20' }}">
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold">{{ $n->title }}</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-200">{{ $n->body }}</p>
                            <time class="text-xs text-gray-500" datetime="{{ optional($n->created_at)->toIso8601String() }}">{{ optional($n->created_at)->diffForHumans() }}</time>
                        </div>
                        <div class="shrink-0">
                            @if(!$n->read_at)
                                <form method="POST" action="{{ route('student.notifications.index') }}">
                                    @csrf
                                    <input type="hidden" name="mark_read_id" value="{{ $n->id }}">
                                    <button type="submit" class="px-3 py-1.5 rounded-md text-xs border hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Mark as read') }}</button>
                                </form>
                            @else
                                <x-student::badge variant="default">{{ __('Read') }}</x-student::badge>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
            @if(method_exists($notifications, 'links'))
                <div class="mt-4">{{ $notifications->links() }}</div>
            @endif
        @endif
    </x-student::card>
@endsection


