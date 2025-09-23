{{--
    @phpdoc
    Variabel:
    - $module: object{id,title,description,order,completed,submodules:Collection,attachments:Collection,nextModule,previousModule}
--}}
@extends('student.layouts.app')

@section('title', $module->title)

@section('content')
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'route' => 'student.dashboard'],
        ['label' => 'Courses', 'route' => 'student.courses.index'],
        ['label' => __('Module') . ' #' . ($module->order ?? '-')],
    ]])

    <x-student::card>
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold">{{ $module->title }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Order') }}: {{ $module->order ?? '-' }}</p>
                <div class="mt-2">
                    @if($module->completed)
                        <x-student::badge variant="success">{{ __('Completed') }}</x-student::badge>
                    @else
                        <x-student::badge>{{ __('In Progress') }}</x-student::badge>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $module->previousModule ? route('student.modules.show', $module->previousModule->id) : '#' }}" class="px-3 py-2 rounded-md border text-sm {{ $module->previousModule ? 'hover:bg-gray-50 dark:hover:bg-gray-700' : 'opacity-50 cursor-not-allowed' }}" @unless($module->previousModule) aria-disabled="true" @endunless>{{ __('Previous') }}</a>
                <a href="{{ $module->nextModule ? route('student.modules.show', $module->nextModule->id) : '#' }}" class="px-3 py-2 rounded-md bg-indigo-600 text-white text-sm {{ $module->nextModule ? 'hover:bg-indigo-700' : 'opacity-50 cursor-not-allowed' }}" @unless($module->nextModule) aria-disabled="true" @endunless>{{ __('Next') }}</a>
            </div>
        </div>

        <div class="mt-4 prose dark:prose-invert max-w-none">
            {!! $module->description !!}
        </div>

        @if(($module->attachments ?? collect())->isNotEmpty())
            <div class="mt-6">
                <h2 class="text-sm font-semibold mb-2">{{ __('Attachments') }}</h2>
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach($module->attachments as $file)
                        <li>
                            <a href="{{ $file->url ?? '#' }}" class="text-indigo-600 hover:underline" target="_blank" rel="noopener">{{ $file->name ?? __('File') }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-6">
            <h2 class="text-sm font-semibold mb-2">{{ __('Lessons') }}</h2>
            @if(($module->submodules ?? collect())->isEmpty())
                <x-student::empty-state :title="__('No lessons yet')" />
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($module->submodules as $sub)
                        <details class="py-3 group">
                            <summary class="cursor-pointer list-none flex items-center justify-between">
                                <span class="text-sm font-medium">{{ $sub->title ?? __('Lesson') }}</span>
                                <span class="text-xs text-gray-600 dark:text-gray-300">{{ __('Order') }}: {{ $sub->order ?? '-' }}</span>
                            </summary>
                            <div class="mt-2 text-sm text-gray-700 dark:text-gray-200">
                                {!! $sub->description ?? '' !!}
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
        </div>
    </x-student::card>
@endsection


