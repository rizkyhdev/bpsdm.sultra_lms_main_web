@extends('layouts.admin')

@section('title', __('Admin Dashboard'))

@section('breadcrumb')
    <li class="text-gray-600 dark:text-gray-400" aria-current="page">{{ __('Dashboard') }}</li>
@endsection

@section('content')
    @php /** @var array{users_total?:int,courses_total?:int,enrollments_total?:int,completion_rate?:int|float} $metrics */ @endphp
    @php /** @var \Illuminate\Support\Collection|array $recentEnrollments */ @endphp
    @php /** @var \Illuminate\Support\Collection|array $recentCertificates */ @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-admin.card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500">{{ __('Total Users') }}</div>
                    <div class="text-2xl font-semibold">{{ $metrics['users_total'] ?? 0 }}</div>
                </div>
                <div class="text-gray-400">★</div>
            </div>
        </x-admin.card>
        <x-admin.card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500">{{ __('Total Courses') }}</div>
                    <div class="text-2xl font-semibold">{{ $metrics['courses_total'] ?? 0 }}</div>
                </div>
                <div class="text-gray-400">★</div>
            </div>
        </x-admin.card>
        <x-admin.card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500">{{ __('Total Enrollments') }}</div>
                    <div class="text-2xl font-semibold">{{ $metrics['enrollments_total'] ?? 0 }}</div>
                </div>
                <div class="text-gray-400">★</div>
            </div>
        </x-admin.card>
        <x-admin.card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500">{{ __('Completion Rate') }}</div>
                    <div class="text-2xl font-semibold">{{ $metrics['completion_rate'] ?? 0 }}%</div>
                </div>
                <div class="text-gray-400">★</div>
            </div>
        </x-admin.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
        <x-admin.card class="lg:col-span-2" :title="__('Recent Enrollments')">
            <x-admin.table :headers="[
                ['key'=>'user','label'=>__('User')],
                ['key'=>'course','label'=>__('Course')],
                ['key'=>'date','label'=>__('Date')],
                ['key'=>'status','label'=>__('Status')],
            ]">
                @forelse(($recentEnrollments ?? []) as $en)
                    <tr>
                        <td class="px-4 py-2">{{ $en->user->nama ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $en->course->judul ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($en->enrollment_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            @if(($en->status ?? null) === 'completed')
                                <x-admin.badge color="green">{{ __('Completed') }}</x-admin.badge>
                            @else
                                <x-admin.badge>{{ $en->status ?? '-' }}</x-admin.badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6">
                            <x-admin.empty-state :title="__('No data')" :description="__('There are no recent enrollments.')" />
                        </td>
                    </tr>
                @endforelse
            </x-admin.table>
        </x-admin.card>

        <x-admin.card :title="__('Recent Certificates')">
            <x-admin.table :headers="[
                ['key'=>'number','label'=>__('Number')],
                ['key'=>'user','label'=>__('User')],
                ['key'=>'actions','label'=>'']
            ]">
                @forelse(($recentCertificates ?? []) as $c)
                    <tr>
                        <td class="px-4 py-2">{{ $c->nomor_sertifikat ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $c->user->nama ?? '-' }}</td>
                        <td class="px-4 py-2 text-right">
                            @if(!empty($c->file_path))
                                <a class="inline-flex items-center px-2 py-1 text-sm rounded border hover:bg-gray-50 dark:hover:bg-gray-800" href="{{ Storage::url($c->file_path) }}" target="_blank" rel="noopener">{{ __('Download') }}</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6">
                            <x-admin.empty-state :title="__('No data')" :description="__('There are no recent certificates.')" />
                        </td>
                    </tr>
                @endforelse
            </x-admin.table>
        </x-admin.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
        <x-admin.card :title="__('Enrollment Trend')">
            <div id="chartEnrollments" class="h-60" data-labels='@json($charts['enrollments']['labels'] ?? [])' data-series='@json($charts['enrollments']['series'] ?? [])'></div>
        </x-admin.card>
        <x-admin.card :title="__('Completion Trend')">
            <div id="chartCompletions" class="h-60" data-labels='@json($charts['completions']['labels'] ?? [])' data-series='@json($charts['completions']['series'] ?? [])'></div>
        </x-admin.card>
    </div>
@endsection


