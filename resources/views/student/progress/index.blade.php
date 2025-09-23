{{--
    @phpdoc
    Variabel:
    - $summary: object dengan total dan data chart-friendly
    - $items: LengthAwarePaginator atau Collection berisi baris progres
--}}
@extends('student.layouts.app')

@section('title', __('Progress'))

@section('content')
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'route' => 'student.dashboard'],
        ['label' => 'Progress'],
    ]])

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-student::card :title="__('Overview')" class="md:col-span-1">
            <dl class="text-sm space-y-2">
                <div class="flex justify-between"><dt>{{ __('Enrolled') }}</dt><dd>{{ $summary->enrolled ?? 0 }}</dd></div>
                <div class="flex justify-between"><dt>{{ __('Completed') }}</dt><dd>{{ $summary->completed ?? 0 }}</dd></div>
                <div class="flex justify-between"><dt>{{ __('In Progress') }}</dt><dd>{{ $summary->in_progress ?? 0 }}</dd></div>
            </dl>
        </x-student::card>
        <x-student::card :title="__('Chart')" class="md:col-span-2">
            <div class="h-48 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400">{{ __('Chart placeholder') }}</div>
        </x-student::card>
    </div>

    <x-student::card class="mt-6" :title="__('Detailed Progress')">
        @if(($items ?? collect())->isEmpty())
            <x-student::empty-state :title="__('No progress records')" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="py-2 pr-4">{{ __('Course') }}</th>
                            <th class="py-2 pr-4">{{ __('Module') }}</th>
                            <th class="py-2 pr-4">{{ __('Progress') }}</th>
                            <th class="py-2">{{ __('Updated') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($items as $row)
                            <tr>
                                <td class="py-2 pr-4">{{ $row->course_title ?? '-' }}</td>
                                <td class="py-2 pr-4">{{ $row->module_title ?? '-' }}</td>
                                <td class="py-2 pr-4 w-64">
                                    <x-student::progress-bar :value="$row->percent ?? 0" />
                                </td>
                                <td class="py-2">{{ optional($row->updated_at)->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(method_exists($items, 'links'))
                <div class="mt-4">{{ $items->links() }}</div>
            @endif
        @endif
    </x-student::card>
@endsection


