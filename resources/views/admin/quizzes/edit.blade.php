@extends('layouts.admin')

@section('title', 'Ubah Kuis')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sub_modules.show', $quiz->sub_module_id) }}">Sub Modul</a></li>
    <li class="breadcrumb-item active">Ubah Kuis</li>
@endsection

@section('content')
    <div class="max-w-4xl mt-6 px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ __('Update Informasi Kuis') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Sesuaikan judul, batas nilai, dan percobaan kuis.') }}</p>
            </div>
            
            <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" class="p-6 md:p-8 space-y-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-5">
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Judul Kuis <span class="text-red-500">*</span></label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul', $quiz->judul) }}" 
                            class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 transition-colors @error('judul') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('judul')<p class="mt-1.5 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="nilai_minimum" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nilai Minimum Kelulusan <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" id="nilai_minimum" name="nilai_minimum" value="{{ old('nilai_minimum', $quiz->nilai_minimum) }}" 
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 transition-colors @error('nilai_minimum') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 font-medium">pts</div>
                            </div>
                            @error('nilai_minimum')<p class="mt-1.5 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="max_attempts" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Maksimal Percobaan <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 transition-colors @error('max_attempts') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 font-medium">&times;</div>
                            </div>
                            @error('max_attempts')<p class="mt-1.5 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi Lengkap / Instruksi Kuis</label>
                        <textarea id="deskripsi" name="deskripsi" rows="5" 
                            class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3 transition-colors @error('deskripsi') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Jelaskan instruksi sebelum kuis dimulai...">{{ old('deskripsi', $quiz->deskripsi) }}</textarea>
                        @error('deskripsi')<p class="mt-1.5 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-100 dark:border-gray-700 mt-6">
                    <a href="{{ route('admin.sub_modules.show', $quiz->sub_module_id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 tracking-wide transition-all shadow-sm">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 tracking-wide transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-save"></i>
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


