@extends('student.layout')

@section('title', 'Daftar Pelatihan')

@section('content')
<div class="space-y-6" x-data="{ viewMode: 'grid' }">
    <!-- Toolbar -->
    <div class="toolbar">
        <div class="flex flex-wrap gap-3 items-center">
            <!-- Search -->
            <form method="GET" action="{{ route('student.pelatihan') }}" class="flex-1 min-w-[200px]" id="search-form">
                <label for="search" class="sr-only">Cari pelatihan</label>
                <input 
                    type="text" 
                    id="search"
                    name="q" 
                    value="{{ $search ?? '' }}" 
                    placeholder="Cari pelatihan..." 
                    onkeyup="if(event.key === 'Enter') this.form.submit()"
                    class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </form>

            <!-- Status Filter -->
            <fieldset class="flex items-center gap-2">
                <legend class="sr-only">Filter status</legend>
                <label for="status" class="sr-only">Status</label>
                <select 
                    id="status"
                    name="status" 
                    onchange="updateFilterForm()"
                    class="px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                    <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="in_progress" {{ ($status ?? '') === 'in_progress' ? 'selected' : '' }}>Sedang Berlangsung</option>
                    <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </fieldset>

            <!-- Sort -->
            <fieldset class="flex items-center gap-2">
                <legend class="sr-only">Urutkan</legend>
                <label for="sort" class="sr-only">Urutkan</label>
                <select 
                    id="sort"
                    name="sort" 
                    onchange="updateFilterForm()"
                    class="px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                    <option value="recent" {{ ($sort ?? 'recent') === 'recent' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                    <option value="progress" {{ ($sort ?? '') === 'progress' ? 'selected' : '' }}>Progress</option>
                    <option value="title" {{ ($sort ?? '') === 'title' ? 'selected' : '' }}>Judul</option>
                </select>
            </fieldset>

            <!-- View Toggle -->
            <div class="flex items-center gap-2 border border-slate-300 rounded-md p-1">
                <button 
                    type="button"
                    @click="viewMode = 'grid'"
                    :class="viewMode === 'grid' ? 'bg-indigo-600 text-white' : 'text-slate-600'"
                    class="px-3 py-1 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    aria-label="Grid view"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                <button 
                    type="button"
                    @click="viewMode = 'list'"
                    :class="viewMode === 'list' ? 'bg-indigo-600 text-white' : 'text-slate-600'"
                    class="px-3 py-1 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    aria-label="List view"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Form (hidden, used for form submission) -->
    <form id="filter-form" method="GET" action="{{ route('student.pelatihan') }}" class="hidden">
        <input type="hidden" name="q" id="filter-q" value="{{ $search ?? '' }}">
        <input type="hidden" name="status" id="filter-status" value="{{ $status ?? 'all' }}">
        <input type="hidden" name="sort" id="filter-sort" value="{{ $sort ?? 'recent' }}">
    </form>

    <script>
        function updateFilterForm() {
            document.getElementById('filter-status').value = document.getElementById('status').value;
            document.getElementById('filter-sort').value = document.getElementById('sort').value;
            document.getElementById('filter-q').value = document.getElementById('search').value;
            document.getElementById('filter-form').submit();
        }
    </script>

    <!-- Course List -->
    @if(isset($enrollmentsWithProgress) && $enrollmentsWithProgress->count() > 0)
        <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($enrollmentsWithProgress as $enrollment)
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $enrollment->course->judul }}</h3>
                    <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ Str::limit($enrollment->course->deskripsi, 100) }}</p>
                    
                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-slate-600 mb-1">
                            <span>Progress</span>
                            <span>{{ $enrollment->progress_percent ?? 0 }}%</span>
                        </div>
                        <x-progress-bar :value="$enrollment->progress_percent ?? 0" />
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('student.courses.show', $enrollment->course->id) }}" class="btn-primary flex-1 text-center">
                            Lanjutkan
                        </a>
                        <a href="{{ route('student.courses.show', $enrollment->course->id) }}" class="px-4 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50">
                            Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div x-show="viewMode === 'list'" x-cloak class="space-y-4">
            @foreach($enrollmentsWithProgress as $enrollment)
                <div class="card p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $enrollment->course->judul }}</h3>
                            <p class="text-sm text-slate-600 mb-4">{{ Str::limit($enrollment->course->deskripsi, 150) }}</p>
                            
                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-slate-600 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $enrollment->progress_percent ?? 0 }}%</span>
                                </div>
                                <x-progress-bar :value="$enrollment->progress_percent ?? 0" />
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('student.courses.show', $enrollment->course->id) }}" class="btn-primary">
                                Lanjutkan
                            </a>
                            <a href="{{ route('student.courses.show', $enrollment->course->id) }}" class="px-4 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <x-empty-state 
            title="Belum ada pelatihan" 
            subtitle="Anda belum terdaftar dalam pelatihan apapun." 
            :action="['label' => 'Jelajahi Pelatihan', 'url' => route('courses.index')]"
        />
    @endif
</div>
@endsection

