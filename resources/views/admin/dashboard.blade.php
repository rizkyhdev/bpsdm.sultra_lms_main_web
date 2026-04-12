@extends('layouts.admin')

@section('title', __('Admin Dashboard'))

@section('breadcrumb')
    <li class="text-gray-600 dark:text-gray-400" aria-current="page">{{ __('Dashboard') }}</li>
@endsection

@section('content')
    @php /** @var array{users_total?:int,courses_total?:int,enrollments_total?:int,completion_rate?:int|float} $metrics */ @endphp
    @php /** @var \Illuminate\Support\Collection|array $recentEnrollments */ @endphp
    @php /** @var \Illuminate\Support\Collection|array $recentCertificates */ @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white transform transition hover:-translate-y-1 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-indigo-100 text-sm font-medium font-sans tracking-wide uppercase">{{ __('Total Users') }}</div>
                    <div class="text-4xl font-bold mt-2">{{ $metrics['users_total'] ?? 0 }}</div>
                </div>
                <div class="p-3 bg-white/20 rounded-xl">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white transform transition hover:-translate-y-1 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-emerald-100 text-sm font-medium font-sans tracking-wide uppercase">{{ __('Total Courses') }}</div>
                    <div class="text-4xl font-bold mt-2">{{ $metrics['courses_total'] ?? 0 }}</div>
                </div>
                <div class="p-3 bg-white/20 rounded-xl">
                    <i class="fas fa-book-open text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl shadow-lg p-6 text-white transform transition hover:-translate-y-1 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-amber-100 text-sm font-medium font-sans tracking-wide uppercase">{{ __('Enrollments') }}</div>
                    <div class="text-4xl font-bold mt-2">{{ $metrics['enrollments_total'] ?? 0 }}</div>
                </div>
                <div class="p-3 bg-white/20 rounded-xl">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white transform transition hover:-translate-y-1 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-purple-100 text-sm font-medium font-sans tracking-wide uppercase">{{ __('Completion Rate') }}</div>
                    <div class="text-4xl font-bold mt-2">{{ $metrics['completion_rate'] ?? 0 }}%</div>
                </div>
                <div class="p-3 bg-white/20 rounded-xl">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
        <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ __('Recent Enrollments') }}</h3>
                <a href="{{ route('admin.enrollments.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">{{ __('View All') }} &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 dark:bg-gray-800/50 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3 font-medium">{{ __('User') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Course') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Date') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse(($recentEnrollments ?? []) as $en)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition duration-150">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white flex items-center gap-3">
                                    <img src="{{ $en->user->avatar_url ?? asset('image/user.png') }}" class="w-8 h-8 rounded-full border border-gray-200" alt="Avatar">
                                    {{ $en->user->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($en->course->judul ?? '-', 40) }}</td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ optional($en->enrollment_date)->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    @if(($en->status ?? null) === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            {{ __('Completed') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                            {{ $en->status ?? '-' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                        <p>{{ __('No recent enrollments.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ __('Recent Certificates') }}</h3>
                <a href="{{ route('admin.certificates.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">{{ __('View All') }} &rarr;</a>
            </div>
            <div class="p-4 space-y-4">
                @forelse(($recentCertificates ?? []) as $c)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-indigo-200 transition">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $c->user->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $c->nomor_sertifikat ?? '-' }}</div>
                            </div>
                        </div>
                        @if(!empty($c->file_path))
                            <a href="{{ Storage::url($c->file_path) }}" target="_blank" rel="noopener" class="flex-shrink-0 text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-500 flex flex-col items-center">
                        <i class="fas fa-award text-3xl mb-2 opacity-50"></i>
                        <p class="text-sm">{{ __('No recent certificates.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Enrollment Trend') }}</h3>
            <div id="chartEnrollments" class="h-[250px] w-full" data-labels='@json($charts["enrollments"]["labels"] ?? [])' data-series='@json($charts["enrollments"]["series"] ?? [])'></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Completion Trend') }}</h3>
            <div id="chartCompletions" class="h-[250px] w-full" data-labels='@json($charts["completions"]["labels"] ?? [])' data-series='@json($charts["completions"]["series"] ?? [])'></div>
        </div>
    </div>
@endsection


