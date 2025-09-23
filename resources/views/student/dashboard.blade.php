{{--
    @phpdoc
    Variabel:
    - $stats = ['enrolled' => int, 'completed' => int, 'in_progress' => int]
    - $recentActivities = Illuminate\Support\Collection<Activity>
--}}
@extends('layouts.studentapp')

@section('title', __('Dashboard'))

@section('content')
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => __('Dashboard')],
    ]])

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <x-student.card :title="__('Enrolled Courses')">
            <p class="text-3xl font-bold">{{ (int)($stats['enrolled'] ?? 0) }}</p>
        </x-student.card>

        <x-student.card :title="__('In Progress')">
            <p class="text-3xl font-bold">{{ (int)($stats['in_progress'] ?? 0) }}</p>
        </x-student.card>

        <x-student.card :title="__('Completed')">
            <p class="text-3xl font-bold">{{ (int)($stats['completed'] ?? 0) }}</p>
        </x-student.card>
    </div>

    <div class="mt-6">
        <x-student.card :title="__('Recent Activity')">
            @if(($recentActivities ?? collect())->isEmpty())
                <x-student.empty-state :title="__('No recent activity')" :description="__('You have no recent learning activity.')">
                    <x-slot:action>
                        <a href="{{ route('student.courses.index') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">{{ __('Browse Courses') }}</a>
                    </x-slot:action>
                </x-student.empty-state>
            @else
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentActivities as $activity)
                        <li class="py-3 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate">{{ $activity->title ?? __('Activity') }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-300 truncate">{{ $activity->description ?? '' }}</p>
                            </div>
                            <time datetime="{{ optional($activity->created_at)->toIso8601String() }}" class="text-xs text-gray-500">{{ optional($activity->created_at)->diffForHumans() }}</time>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-student.card>
    </div>
@endsection

