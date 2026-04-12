<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Courses') }} - {{ config('app.name', 'Sobat AURA') }}</title>
    
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
    <link rel="icon" href="{{ asset('image/LOGO AURA 1.png') }}" type="image/png">
    
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
    }" class="min-h-screen flex-fill">
        <div class="container mx-auto px-4 py-6">
            <form id="filter-form" method="GET" action="{{ route('courses.index') }}" class="hidden">
                <input type="text" name="q" x-model="q">
                <input type="text" name="sort" x-model="sort">
                <input type="text" name="view" x-model="view">
                <template x-for="cat in categories">
                    <input type="hidden" name="categories[]" :value="cat">
                </template>
                <template x-for="diff in difficulty">
                    <input type="hidden" name="difficulty[]" :value="diff">
                </template>
                <input type="hidden" name="rating" x-model="rating">
            </form>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                {{-- Sidebar Filters (md+) --}}
                <aside class="md:col-span-3">
                    <div class="bg-white rounded-3 shadow-sm border-0 p-4 sticky top-4">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                            <i class="fas fa-filter text-primary me-2"></i>
                            <h2 class="text-lg font-bold text-dark mb-0">Filter Pelatihan</h2>
                        </div>

                        {{-- Categories --}}
                        <fieldset class="mb-4">
                            <button type="button" @click="categoriesOpen = !categoriesOpen" class="flex items-center justify-between w-full text-left font-semibold text-gray-800 mb-2" :aria-expanded="categoriesOpen">
                                <span>Bidang Kompetensi</span>
                                <i class="fas fa-chevron-down small transition-transform" :class="{ 'rotate-180': categoriesOpen }"></i>
                            </button>
                            <div class="space-y-2 mt-2" x-show="categoriesOpen" x-transition x-cloak>
                                @foreach ($categories as $category)
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="checkbox" 
                                               :checked="categories.includes('{{ $category }}')"
                                               @change="toggleCategory('{{ $category }}')"
                                               class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                                        <span class="ml-2 text-sm text-gray-600 group-hover:text-dark transition-colors">{{ $category }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        {{-- Difficulty --}}
                        <fieldset class="mb-4">
                            <button type="button" @click="difficultyOpen = !difficultyOpen" class="flex items-center justify-between w-full text-left font-semibold text-gray-800 mb-2" :aria-expanded="difficultyOpen">
                                <span>Tingkat Kesulitan</span>
                                <i class="fas fa-chevron-down small transition-transform" :class="{ 'rotate-180': difficultyOpen }"></i>
                            </button>
                            <div class="space-y-2 mt-2" x-show="difficultyOpen" x-transition x-cloak>
                                @foreach ($difficulties as $diff)
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="checkbox" 
                                               :checked="difficulty.includes('{{ $diff }}')"
                                               @change="toggleDifficulty('{{ $diff }}')"
                                               class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                                        <span class="ml-2 text-sm text-gray-600 group-hover:text-dark transition-colors">{{ $diff }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        {{-- Rating --}}
                        <fieldset class="mb-4">
                            <button type="button" @click="ratingOpen = !ratingOpen" class="flex items-center justify-between w-full text-left font-semibold text-gray-800 mb-2" :aria-expanded="ratingOpen">
                                <span>Penilaian</span>
                                <i class="fas fa-chevron-down small transition-transform" :class="{ 'rotate-180': ratingOpen }"></i>
                            </button>
                            <div class="space-y-2 mt-2" x-show="ratingOpen" x-transition x-cloak>
                                @for ($i = 5; $i >= 1; $i--)
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="radio" 
                                               name="rating_radio"
                                               :checked="rating === '{{ $i }}'"
                                               @change="setRating('{{ $i }}')"
                                               class="text-primary focus:ring-primary h-4 w-4">
                                        <div class="ml-2 flex items-center">
                                            @for ($j = 0; $j < 5; $j++)
                                                <i class="fas fa-star text-sm {{ $j < $i ? 'text-warning' : 'text-gray-200' }}"></i>
                                            @endfor
                                            <span class="ml-2 text-xs text-gray-500">{{ $i }} Bintang</span>
                                        </div>
                                    </label>
                                @endfor
                            </div>
                        </fieldset>

                        {{-- Reset Filters --}}
                        <button type="button" @click="resetFilters()" class="w-full mt-2 px-4 py-2 bg-light text-dark font-semibold rounded-pill border hover:bg-white hover:border-primary transition-all duration-200">
                             Hapus Semua Filter
                        </button>
                    </div>
                </aside>

                {{-- Main Content --}}
                <main class="md:col-span-9">
                    {{-- Toolbar --}}
                    <div class="bg-white rounded-3 shadow-sm border-0 p-4 mb-6">
                        <div class="d-flex flex-column lg:flex-row gap-3 align-items-center justify-content-between">
                            {{-- Search --}}
                            <div class="flex-grow-1 w-full">
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400 group-focus-within:text-primary transition-colors"></i>
                                    </div>
                                    <input type="text" 
                                           id="search" 
                                           x-model="q"
                                           @keyup.enter="submitForm()"
                                           placeholder="Cari pelatihan yang Anda inginkan..."
                                           class="block w-full pl-10 pr-4 py-2 border-0 bg-light rounded-pill focus:ring-2 focus:ring-primary focus:bg-white transition-all text-sm">
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                                {{-- Sort --}}
                                <div class="flex items-center bg-light rounded-pill px-3 py-1">
                                    <i class="fas fa-sort-amount-down text-gray-400 mr-2 text-sm"></i>
                                    <select id="sort" 
                                            x-model="sort"
                                            @change="changeSort()"
                                            class="bg-transparent border-0 focus:ring-0 text-sm font-medium text-gray-700 py-1">
                                        <option value="latest">Terbaru</option>
                                        <option value="oldest">Terlama</option>
                                        <option value="highest_rated">Rating Tertinggi</option>
                                        <option value="most_popular">Paling Populer</option>
                                    </select>
                                </div>

                                {{-- View Toggle --}}
                                <div class="flex items-center bg-gray-100 rounded-pill p-1 shadow-inner">
                                    <button type="button" 
                                            @click="changeView('grid')"
                                            :class="view === 'grid' ? 'bg-white shadow-sm text-primary' : 'text-gray-500 hover:text-dark'"
                                            class="w-10 h-8 flex items-center justify-center rounded-pill transition-all duration-200">
                                        <i class="fas fa-th-large"></i>
                                    </button>
                                    <button type="button" 
                                            @click="changeView('list')"
                                            :class="view === 'list' ? 'bg-white shadow-sm text-primary' : 'text-gray-500 hover:text-dark'"
                                            class="w-10 h-8 flex items-center justify-center rounded-pill transition-all duration-200">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Results --}}
                    @if ($courses->count() > 0)
                        <div x-show="view === 'grid'" x-cloak class="row g-4">
                            @foreach ($courses as $course)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <x-course-card :course="$course" view="grid" :actions="true" />
                                </div>
                            @endforeach
                        </div>
                        <div x-show="view === 'list'" x-cloak x-transition class="space-y-4">
                            @foreach ($courses as $course)
                                <x-course-card :course="$course" view="list" />
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8 d-flex justify-content-center">
                            {{ $courses->links() }}
                        </div>
                    @else
                        <div class="bg-white rounded-4 shadow-sm py-12 px-6 text-center">
                            <div class="bg-light rounded-circle w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search-minus text-4xl text-gray-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Pelatihan Tidak Ditemukan</h3>
                            <p class="text-gray-500 max-w-md mx-auto mb-6">
                                Maaf, pelatihan yang Anda cari tidak tersedia. Coba gunakan kata kunci lain atau bersihkan filter yang aktif.
                            </p>
                            <button @click="resetFilters()" class="btn btn-primary rounded-pill px-6 py-2">
                                <i class="fas fa-sync-alt me-2"></i>Lihat Semua Pelatihan
                            </button>
                        </div>
                    @endif
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

