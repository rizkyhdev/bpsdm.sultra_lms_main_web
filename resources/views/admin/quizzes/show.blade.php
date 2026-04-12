@extends('layouts.admin')

@section('title', 'Detail Kuis')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sub_modules.show', $quiz->sub_module_id) }}">Sub Modul</a></li>
    <li class="breadcrumb-item active">Detail Kuis</li>
@endsection

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-100 hover:bg-emerald-200 rounded-lg transition-colors">
            <i class="fas fa-edit"></i> Ubah
        </a>
        <a href="{{ route('admin.questions.index', $quiz) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200 rounded-lg transition-colors">
            <i class="fas fa-question-circle"></i> Kelola Pertanyaan
        </a>
        <a href="{{ route('admin.quizzes.results', $quiz) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-lg transition-colors">
            <i class="fas fa-chart-bar"></i> Hasil
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-6xl mt-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-white/10 pattern-dots opacity-50"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:justify-between md:items-end">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 text-white text-xs font-semibold uppercase tracking-wider mb-3 backdrop-blur-sm">
                            <i class="fas fa-stopwatch"></i> Kuis Modul
                        </div>
                        <h1 class="text-3xl font-bold">{{ $quiz->judul }}</h1>
                        <p class="mt-2 text-indigo-100 max-w-2xl text-sm">{{ \Illuminate\Support\Str::limit($quiz->deskripsi, 150) }}</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex gap-4 text-center">
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3 min-w-[100px]">
                            <div class="text-xs text-indigo-100 uppercase tracking-widest">{{ __('Min Nilai') }}</div>
                            <div class="text-2xl font-bold mt-1">{{ $quiz->nilai_minimum }}</div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3 min-w-[100px]">
                            <div class="text-xs text-indigo-100 uppercase tracking-widest">{{ __('Percobaan') }}</div>
                            <div class="text-2xl font-bold mt-1">{{ $quiz->max_attempts }} &times;</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">{{ __('Deskripsi Lengkap') }}</h3>
                <div class="prose prose-indigo max-w-none text-gray-600 dark:text-gray-300">
                    {!! nl2br(e($quiz->deskripsi)) !!}
                </div>
            </div>
            
            <div class="bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 px-6 py-4 flex justify-end">
                <span class="text-sm text-gray-500">
                    ID Modul Terkait: #{{ $quiz->sub_module_id }}
                </span>
            </div>
        </div>
    </div>
@endsection


