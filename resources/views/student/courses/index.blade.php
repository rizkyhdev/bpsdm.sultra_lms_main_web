{{--
    @phpdoc
    Variabel:
    - $courses = Collection<object{id,title,short_description,cover_url,progress_percent,instructor_name,updated_at}>
--}}
@extends('layouts.studentapp')

@section('title', __('Courses'))

@section('content')
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => __('Dashboard'), 'route' => 'student.dashboard'],
        ['label' => __('Courses')],
    ]])

    @if(($courses ?? collect())->isEmpty())
        <x-student.card>
            <x-student.empty-state :title="__('You are not enrolled in any course')" :description="__('Enroll to start learning.')">
                <x-slot:action>
                    <a href="#" class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">{{ __('Explore Catalog') }}</a>
                </x-slot:action>
            </x-student.empty-state>
        </x-student.card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <a href="{{ route('student.courses.show', $course->id) }}" class="group">
                    <x-student.card>
                        <img src="{{ $course->cover_url }}" alt="{{ __('Course cover') }}" class="w-full h-40 object-cover rounded-md mb-3">
                        <h3 class="text-base font-semibold group-hover:underline">{{ $course->title }}</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ $course->short_description }}</p>
                        <div class="mt-3">
                            <x-student.progress-bar :value="$course->progress_percent" :label="__('Progress')" />
                        </div>
                        <div class="mt-3 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span>{{ __('By') }} {{ $course->instructor_name }}</span>
                            <time datetime="{{ optional($course->updated_at)->toIso8601String() }}">{{ __('Updated') }} {{ optional($course->updated_at)->diffForHumans() }}</time>
                        </div>
                    </x-student.card>
                </a>
            @endforeach
        </div>

        @if(method_exists($courses, 'links'))
            <div class="mt-6">
                {{ $courses->links() }}
            </div>
        @endif
    @endif
@endsection


