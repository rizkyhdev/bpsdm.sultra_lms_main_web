{{--
    @phpdoc
    Variabel:
    - $course: object{id,title,description,cover_url,progress_percent,instructor_name,modules: Collection<Module>}
--}}
@extends('layouts.studentapp')

@section('title', $course->title)

@section('content')
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => __('Dashboard'), 'route' => 'student.dashboard'],
        ['label' => __('Courses'), 'route' => 'student.courses.index'],
        ['label' => $course->title],
    ]])

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-student.card>
                <div class="flex flex-col sm:flex-row gap-4">
                    <img src="{{ $course->cover_url }}" alt="{{ __('Course cover') }}" class="w-full sm:w-56 h-40 object-cover rounded-md">
                    <div class="flex-1">
                        <h1 class="text-xl font-bold">{{ $course->title }}</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('By') }} {{ $course->instructor_name }}</p>
                        <div class="mt-3">
                            <x-student.progress-bar :value="$course->progress_percent" :label="__('Overall progress')" />
                        </div>
                    </div>
                </div>
                <div class="mt-4 prose dark:prose-invert max-w-none">
                    {{-- Konten HTML dipercaya dari backend jika difilter: --}}
                    {!! $course->description !!}
                </div>
            </x-student.card>

            <x-student.card class="mt-6" :title="__('Modules')">
                @if(($course->modules ?? collect())->isEmpty())
                    <x-student.empty-state :title="__('No modules yet')" />
                @else
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($course->modules as $module)
                            <li class="py-3 flex items-center justify-between">
                                <div>
                                    <a href="{{ route('student.modules.show', $module->id) }}" class="text-sm font-medium hover:underline">{{ $module->title }}</a>
                                    <p class="text-xs text-gray-600 dark:text-gray-300">{{ __('Order') }}: {{ $module->order ?? '-' }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($module->completed ?? false)
                                        <x-student.badge variant="success">{{ __('Completed') }}</x-student.badge>
                                    @else
                                        <x-student.badge>{{ __('In Progress') }}</x-student.badge>
                                    @endif
                                    <a href="{{ route('student.modules.show', $module->id) }}" class="text-xs text-indigo-600 hover:underline">{{ __('View') }}</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-student.card>
        </div>
        <div>
            <x-student.card :title="__('Course Info')">
                <dl class="text-sm space-y-2">
                    <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-300">{{ __('Instructor') }}</dt><dd>{{ $course->instructor_name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-300">{{ __('Progress') }}</dt><dd>{{ (int)$course->progress_percent }}%</dd></div>
                </dl>
            </x-student.card>
        </div>
    </div>
@endsection


