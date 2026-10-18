<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Courses') }} - {{ config('app.name', 'Sobat ASR') }}</title>
    
    <!-- Bootstrap CSS (required for course-card component) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- custom css -->
    <link href="{{ asset('./css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('./css/custom-style.css') }}" rel="stylesheet">
    <link href="{{ asset('./css/pelatihan.css') }}" rel="stylesheet">
    
    <!-- Font Awesome for Icons (via CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="icon" href="{{ asset('image/logofont.png') }}" type="image/png">
    
    <!-- Tailwind CSS CDN as fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Vite for development -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* Ensure Alpine.js x-show works properly */
        [x-cloak] { display: none !important; }
        
        /* Hover card effect to match student dashboard */
        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    @include('layouts.partials.courses.header')
    
    <div x-data="{
        view: localStorage.getItem('course_view') || '{{ request('view', 'grid') }}',
        categoriesOpen: true,
        difficultyOpen: true,
        ratingOpen: true,
        categories: {{ json_encode(request('categories', [])) }},
        difficulty: {{ json_encode(request('difficulty', [])) }},
        rating: '{{ request('rating', '') }}',
        sort: '{{ request('sort', 'latest') }}',
        q: '{{ request('q', '') }}',
        submitForm() {
            const form = document.getElementById('filter-form');
            form.submit();
        },
        toggleCategory(category) {
            const index = this.categories.indexOf(category);
            if (index > -1) {
                this.categories.splice(index, 1);
            } else {
                this.categories.push(category);
            }
            this.submitForm();
        },
        toggleDifficulty(diff) {
            const index = this.difficulty.indexOf(diff);
            if (index > -1) {
                this.difficulty.splice(index, 1);
            } else {
                this.difficulty.push(diff);
            }
            this.submitForm();
        },
        setRating(r) {
            this.rating = this.rating === r ? '' : r;
            this.submitForm();
        },
        changeSort() {
            this.submitForm();
        },
        changeView(v) {
            this.view = v;
            localStorage.setItem('course_view', v);
        },
        resetFilters() {
            this.categories = [];
            this.difficulty = [];
            this.rating = '';
            this.q = '';
            this.sort = 'latest';
            this.view = 'grid';
            localStorage.setItem('course_view', 'grid');
            this.submitForm();
        }
    }" class="flex-fill py-5">
        <div class="container">
            {{-- Hidden Filter Form --}}
            <form id="filter-form" method="GET" action="{{ route('courses.index') }}" class="d-none">
                <input type="hidden" name="q" x-model="q">
                <input type="hidden" name="sort" x-model="sort">
                <input type="hidden" name="view" x-model="view">
                <template x-for="cat in categories">
                    <input type="hidden" name="categories[]" :value="cat">
                </template>
                <template x-for="diff in difficulty">
                    <input type="hidden" name="difficulty[]" :value="diff">
                </template>
                <input type="hidden" name="rating" x-model="rating">
            </form>

            <div class="row g-4">
                {{-- Sidebar Filters --}}
                <aside class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                            <i class="fas fa-filter text-primary me-2"></i>
                            <h5 class="fw-bold text-dark mb-0">Filter Pelatihan</h5>
                        </div>

                        {{-- Categories --}}
                        <div class="filter-group mb-4">
                            <button type="button" @click="categoriesOpen = !categoriesOpen" class="btn btn-link p-0 w-100 text-decoration-none d-flex align-items-center justify-content-between text-dark fw-bold mb-3 shadow-none">
                                <span style="font-size: 0.95rem;">Bidang Kompetensi</span>
                                <i class="fas fa-chevron-down small transition-transform" :class="{ 'rotate-180': categoriesOpen }"></i>
                            </button>
                            <div class="ps-1" x-show="categoriesOpen" x-transition x-cloak>
                                @foreach ($categories as $category)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input shadow-none cursor-pointer" 
                                               type="checkbox" 
                                               id="cat-{{ $loop->index }}"
                                               :checked="categories.includes('{{ $category }}')"
                                               @change="toggleCategory('{{ $category }}')">
                                        <label class="form-check-label small text-secondary cursor-pointer ms-1" for="cat-{{ $loop->index }}">
                                            {{ $category }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Difficulty --}}
                        <div class="filter-group mb-4">
                            <button type="button" @click="difficultyOpen = !difficultyOpen" class="btn btn-link p-0 w-100 text-decoration-none d-flex align-items-center justify-content-between text-dark fw-bold mb-3 shadow-none">
                                <span style="font-size: 0.95rem;">Tingkat Kesulitan</span>
                                <i class="fas fa-chevron-down small transition-transform" :class="{ 'rotate-180': difficultyOpen }"></i>
                            </button>
                            <div class="ps-1" x-show="difficultyOpen" x-transition x-cloak>
                                @foreach ($difficulties as $diff)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input shadow-none cursor-pointer" 
                                               type="checkbox" 
                                               id="diff-{{ $loop->index }}"
                                               :checked="difficulty.includes('{{ $diff }}')"
                                               @change="toggleDifficulty('{{ $diff }}')">
                                        <label class="form-check-label small text-secondary cursor-pointer ms-1" for="diff-{{ $loop->index }}">
                                            {{ $diff }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Rating --}}
                        <div class="filter-group mb-4">
                            <button type="button" @click="ratingOpen = !ratingOpen" class="btn btn-link p-0 w-100 text-decoration-none d-flex align-items-center justify-content-between text-dark fw-bold mb-3 shadow-none">
                                <span style="font-size: 0.95rem;">Penilaian</span>
                                <i class="fas fa-chevron-down small transition-transform" :class="{ 'rotate-180': ratingOpen }"></i>
                            </button>
                            <div class="ps-1" x-show="ratingOpen" x-transition x-cloak>
                                @for ($i = 5; $i >= 1; $i--)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input shadow-none cursor-pointer" 
                                               type="radio" 
                                               name="rating_radio"
                                               id="rate-{{ $i }}"
                                               :checked="rating === '{{ $i }}'"
                                               @change="setRating('{{ $i }}')">
                                        <label class="form-check-label d-flex align-items-center cursor-pointer ms-1" for="rate-{{ $i }}">
                                            <div class="d-flex align-items-center gap-1">
                                                @for ($j = 0; $j < 5; $j++)
                                                    <i class="fas fa-star" style="font-size: 0.75rem; color: {{ $j < $i ? '#ffc107' : '#e9ecef' }}"></i>
                                                @endfor
                                                <span class="ms-2 text-muted" style="font-size: 0.8rem;">{{ $i }} {{ __('Bintang') }}</span>
                                            </div>
                                        </label>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        {{-- Reset --}}
                        <button type="button" @click="resetFilters()" class="btn btn-outline-primary w-100 rounded-pill btn-sm fw-bold py-2 mt-2">
                            <i class="fas fa-sync-alt me-2"></i>Hapus Semua Filter
                        </button>
                    </div>
                </aside>

                {{-- Main Content --}}
                <main class="col-lg-9">
                    {{-- Toolbar Card --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <div class="row align-items-center g-3">
                            {{-- Search field --}}
                            <div class="col-lg-6">
                                <div class="position-relative">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control rounded-pill border-0 bg-light ps-5 py-2 shadow-none" 
                                           x-model="q"
                                           @keyup.enter="submitForm()"
                                           placeholder="Cari pelatihan yang Anda inginkan...">
                                </div>
                            </div>

                            <div class="col-lg-6 d-flex flex-wrap justify-content-lg-end align-items-center gap-3">
                                {{-- Sort Dropdown --}}
                                <div class="d-flex align-items-center bg-light rounded-pill px-3 py-1">
                                    <i class="fas fa-sort-amount-down text-muted me-2 small"></i>
                                    <select class="form-select form-select-sm border-0 bg-transparent py-1 shadow-none fw-semibold text-dark" 
                                            style="width: auto; font-size: 0.85rem;"
                                            x-model="sort"
                                            @change="changeSort()">
                                        <option value="latest">Terbaru</option>
                                        <option value="oldest">Terlama</option>
                                        <option value="highest_rated">Rating Tertinggi</option>
                                        <option value="most_popular">Paling Populer</option>
                                    </select>
                                </div>

                                {{-- View Toggles --}}
                                <div class="bg-light rounded-pill p-1 d-flex gap-1">
                                    <button type="button" 
                                            @click="changeView('grid')"
                                            :class="view === 'grid' ? 'bg-white shadow-sm text-primary' : 'text-muted'"
                                            class="btn btn-sm rounded-pill border-0 px-3 py-1 transition-all">
                                        <i class="fas fa-th-large"></i>
                                    </button>
                                    <button type="button" 
                                            @click="changeView('list')"
                                            :class="view === 'list' ? 'bg-white shadow-sm text-primary' : 'text-muted'"
                                            class="btn btn-sm rounded-pill border-0 px-3 py-1 transition-all">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Results Area --}}
                    <div class="results-container">
                        @if ($courses->count() > 0)
                            {{-- Grid View --}}
                            <div x-show="view === 'grid'" x-cloak x-transition>
                                <div class="row g-4">
                                    @foreach ($courses as $course)
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <x-course-card :course="$course" view="grid" :actions="true" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- List View --}}
                            <div x-show="view === 'list'" x-cloak x-transition>
                                <div class="d-flex flex-column gap-3">
                                    @foreach ($courses as $course)
                                        <x-course-card :course="$course" view="list" :actions="true" />
                                    @endforeach
                                </div>
                            </div>

                            {{-- Pagination Integration --}}
                            <div class="mt-5 d-flex justify-content-center">
                                {!! $courses->links() !!}
                            </div>
                        @else
                            {{-- Empty State --}}
                            <div class="card border-0 shadow-sm rounded-4 py-5 px-4 text-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                                    <i class="fas fa-search-minus fs-1 text-muted opacity-50"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Pelatihan Tidak Ditemukan</h4>
                                <p class="text-secondary mx-auto mb-4" style="max-width: 450px;">
                                    Maaf, kami tidak dapat menemukan pelatihan yang sesuai dengan kriteria Anda. Cari dengan kata kunci lain atau bersihkan semua filter.
                                </p>
                                <button type="button" @click="resetFilters()" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">
                                    <i class="fas fa-redo-alt me-2"></i>Lihat Semua Pelatihan
                                </button>
                            </div>
                        @endif
                    </div>
                </main>
            </div>
        </div>
    </div>
    
    @include('layouts.partials.courses.footer')
    
    <!-- JS files -->
    <script src="{{ asset('./js/sidebar-toggle.js') }}"></script>
    <script src="{{ asset('./js/page-transition.js') }}"></script>
    <script src="{{ asset('./js/tabs.js') }}"></script>
</body>
</html>

