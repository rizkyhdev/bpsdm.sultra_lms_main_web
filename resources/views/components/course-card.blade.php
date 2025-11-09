@props(['course', 'view' => 'grid'])

@php
    $hasRoute = Route::has('courses.show');
    $instructorEmail = 'rizkyhatsa.dev@gmail.com';
    if (isset($course->owner) && $course->owner) {
        $instructorEmail = $course->owner->email;
    } elseif (isset($course->userEnrollments) && $course->userEnrollments->isNotEmpty()) {
        $firstEnrollment = $course->userEnrollments->first();
        if ($firstEnrollment && isset($firstEnrollment->user) && $firstEnrollment->user) {
            $instructorEmail = $firstEnrollment->user->email;
        }
    }
@endphp

@if ($view === 'grid')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
        {{-- Header/Thumbnail --}}
        <div class="relative h-40 bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center">
            @if (isset($course->cover_url) && $course->cover_url)
                <img src="{{ $course->cover_url }}" alt="{{ $course->judul }}" class="w-full h-full object-cover">
            @else
                <svg class="w-16 h-16 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            @endif
            
            {{-- Category Tag --}}
            @if ($course->bidang_kompetensi)
                <div class="absolute top-2 left-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 uppercase">
                        {{ $course->bidang_kompetensi }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="p-4">
            {{-- Title --}}
            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                {{ $course->judul }}
            </h3>

            {{-- Instructor --}}
            <div class="flex items-center text-sm text-gray-600 mb-3">
                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="truncate">{{ $instructorEmail }}</span>
            </div>

            {{-- Features --}}
            <ul class="space-y-1.5 mb-4">
                @if ($course->modules_count ?? 0)
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $course->modules_count ?? 0 }} {{ __('Modules') }}</span>
                    </li>
                @endif
                @if ($course->sub_modules_count ?? 0)
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $course->sub_modules_count ?? 0 }} {{ __('Sub Modules') }}</span>
                    </li>
                @endif
                @if ($course->contents_count ?? 0)
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $course->contents_count ?? 0 }} {{ __('Contents') }}</span>
                    </li>
                @endif
            </ul>

            {{-- Meta Info --}}
            <div class="flex items-center gap-4 text-xs text-gray-500 mb-4 pb-4 border-b border-gray-200">
                @if ($course->jp_value)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $course->jp_value }} JP</span>
                    </div>
                @endif
                @if ($course->contents_count ?? 0)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>{{ $course->contents_count ?? 0 }} {{ __('Lessons') }}</span>
                    </div>
                @endif
                @if (isset($course->difficulty))
                    <div class="flex items-center">
                        <span>{{ $course->difficulty }}</span>
                    </div>
                @endif
                @if ($course->enrollments_count ?? 0)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>{{ $course->enrollments_count ?? 0 }}</span>
                    </div>
                @endif
            </div>

            {{-- CTA Button --}}
            @if ($hasRoute)
                <a href="{{ route('courses.show', $course->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    {{ __('Start Course') }}
                </a>
            @else
                <button disabled class="block w-full text-center px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">
                    {{ __('Start Course') }}
                </button>
            @endif
        </div>
    </div>
@else
    {{-- List View --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
        <div class="flex flex-col lg:flex-row">
            {{-- Thumbnail --}}
            <div class="relative w-full lg:w-48 h-40 lg:h-auto bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center flex-shrink-0">
                @if (isset($course->cover_url) && $course->cover_url)
                    <img src="{{ $course->cover_url }}" alt="{{ $course->judul }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-16 h-16 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                @endif
                
                @if ($course->bidang_kompetensi)
                    <div class="absolute top-2 left-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 uppercase">
                            {{ $course->bidang_kompetensi }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 p-4 lg:p-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $course->judul }}
                        </h3>
                        <div class="flex items-center text-sm text-gray-600 mb-3">
                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>{{ $instructorEmail }}</span>
                        </div>
                    </div>
                    @if ($hasRoute)
                        <a href="{{ route('courses.show', $course->id) }}" class="mt-3 lg:mt-0 lg:ml-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 whitespace-nowrap">
                            {{ __('Start Course') }}
                        </a>
                    @else
                        <button disabled class="mt-3 lg:mt-0 lg:ml-4 inline-block px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed whitespace-nowrap">
                            {{ __('Start Course') }}
                        </button>
                    @endif
                </div>

                {{-- Features --}}
                <ul class="flex flex-wrap gap-4 mb-3 text-sm text-gray-600">
                    @if ($course->modules_count ?? 0)
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $course->modules_count ?? 0 }} {{ __('Modules') }}</span>
                        </li>
                    @endif
                    @if ($course->contents_count ?? 0)
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $course->contents_count ?? 0 }} {{ __('Lessons') }}</span>
                        </li>
                    @endif
                    @if (isset($course->difficulty))
                        <li class="flex items-center">
                            <span>{{ $course->difficulty }}</span>
                        </li>
                    @endif
                </ul>

                {{-- Meta Info --}}
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    @if ($course->jp_value)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $course->jp_value }} JP</span>
                        </div>
                    @endif
                    @if ($course->enrollments_count ?? 0)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>{{ $course->enrollments_count ?? 0 }} {{ __('Students') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

