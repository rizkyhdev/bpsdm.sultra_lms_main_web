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
        view: '{{ request('view', 'grid') }}',
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
            this.submitForm();
        },
        resetFilters() {
            this.categories = [];
            this.difficulty = [];
            this.rating = '';
            this.q = '';
            this.sort = 'latest';
            this.view = 'grid';
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
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sticky top-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Pelatihan') }}</h2>

                        {{-- Categories --}}
                        <fieldset class="mb-6">
                            <button type="button" @click="categoriesOpen = !categoriesOpen" class="flex items-center justify-between w-full text-left font-medium text-gray-900 mb-3 md:mb-2" :aria-expanded="categoriesOpen" aria-controls="categories-filter">
                                <span>{{ __('Categories') }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': categoriesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="categories-filter" class="space-y-2" x-show="categoriesOpen" x-cloak>
                                @foreach ($categories as $category)
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               :checked="categories.includes('{{ $category }}')"
                                               @change="toggleCategory('{{ $category }}')"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $category }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        {{-- Difficulty --}}
                        <fieldset class="mb-6">
                            <button type="button" @click="difficultyOpen = !difficultyOpen" class="flex items-center justify-between w-full text-left font-medium text-gray-900 mb-3 md:mb-2" :aria-expanded="difficultyOpen" aria-controls="difficulty-filter">
                                <span>{{ __('Difficulty') }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': difficultyOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="difficulty-filter" class="space-y-2" x-show="difficultyOpen" x-cloak>
                                @foreach ($difficulties as $diff)
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               :checked="difficulty.includes('{{ $diff }}')"
                                               @change="toggleDifficulty('{{ $diff }}')"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $diff }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        {{-- Rating --}}
                        <fieldset class="mb-6">
                            <button type="button" @click="ratingOpen = !ratingOpen" class="flex items-center justify-between w-full text-left font-medium text-gray-900 mb-3 md:mb-2" :aria-expanded="ratingOpen" aria-controls="rating-filter">
                                <span>{{ __('Rating') }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': ratingOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="rating-filter" class="space-y-2" x-show="ratingOpen" x-cloak>
                                @for ($i = 5; $i >= 1; $i--)
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" 
                                               name="rating_radio"
                                               :checked="rating === '{{ $i }}'"
                                               @change="setRating('{{ $i }}')"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-2 flex items-center">
                                            @for ($j = 0; $j < 5; $j++)
                                                @if ($j < $i)
                                                    <svg class="w-4 h-4 text-yellow-400 fill-current" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-300 fill-current" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @endif
                                            @endfor
                                            <span class="ml-1 text-sm text-gray-700">{{ $i }} {{ __('star') }}</span>
                                        </div>
                                    </label>
                                @endfor
                            </div>
                        </fieldset>

                        {{-- Reset Filters --}}
                        <button type="button" @click="resetFilters()" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            {{ __('Reset Filters') }}
                        </button>
                    </div>
                </aside>

                {{-- Main Content --}}
                <main class="md:col-span-9">
                    {{-- Toolbar --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                            {{-- Search --}}
                            <div class="flex-1 w-full sm:w-auto">
                                <label for="search" class="sr-only">{{ __('Search courses') }}</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           id="search" 
                                           x-model="q"
                                           @keyup.enter="submitForm()"
                                           placeholder="{{ __('Search courses...') }}"
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>

                            {{-- Search Button --}}
                            <button type="button" @click="submitForm()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                                {{ __('Search') }}
                            </button>

                            {{-- Sort --}}
                            <div class="flex items-center">
                                <label for="sort" class="sr-only">{{ __('Sort by') }}</label>
                                <select id="sort" 
                                        x-model="sort"
                                        @change="changeSort()"
                                        class="block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="latest">{{ __('Latest First') }}</option>
                                    <option value="oldest">{{ __('Oldest First') }}</option>
                                    <option value="highest_rated">{{ __('Highest Rated') }}</option>
                                    <option value="most_popular">{{ __('Most Popular') }}</option>
                                </select>
                            </div>

                            {{-- View Toggle --}}
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-700">{{ __('Select View:') }}</label>
                                <div class="flex gap-1 border border-gray-300 rounded-md p-1">
                                    <button type="button" 
                                            @click="changeView('list')"
                                            :aria-pressed="view === 'list'"
                                            :class="view === 'list' ? 'bg-green-500 text-white' : 'bg-white text-gray-700'"
                                            class="p-1.5 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200"
                                            aria-label="{{ __('List view') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                        </svg>
                                    </button>
                                    <button type="button" 
                                            @click="changeView('grid')"
                                            :aria-pressed="view === 'grid'"
                                            :class="view === 'grid' ? 'bg-green-500 text-white' : 'bg-white text-gray-700'"
                                            class="p-1.5 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200"
                                            aria-label="{{ __('Grid view') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                        </svg>
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
                        <div x-show="view === 'list'" x-cloak class="space-y-4">
                            @foreach ($courses as $course)
                                <x-course-card :course="$course" view="list" />
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $courses->links() }}
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No courses found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Try adjusting your filters or search terms.') }}</p>
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

