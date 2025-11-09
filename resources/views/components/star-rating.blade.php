@props(['rating' => 0, 'maxRating' => 5, 'showHalf' => false])

@php
    $rating = (float) $rating;
    $fullStars = (int) floor($rating);
    $hasHalfStar = $showHalf && ($rating - $fullStars) >= 0.5;
    $emptyStars = $maxRating - $fullStars - ($hasHalfStar ? 1 : 0);
@endphp

<div class="flex items-center gap-0.5" role="img" aria-label="{{ __('Rating: :rating out of :max', ['rating' => $rating, 'max' => $maxRating]) }}">
    @for ($i = 0; $i < $fullStars; $i++)
        <svg class="w-4 h-4 text-yellow-400 fill-current" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
        </svg>
    @endfor
    
    @if ($hasHalfStar)
        <div class="relative w-4 h-4">
            <svg class="w-4 h-4 text-gray-300 fill-current absolute" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
            </svg>
            <svg class="w-4 h-4 text-yellow-400 fill-current absolute" style="clip-path: inset(0 50% 0 0);" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
            </svg>
        </div>
    @endif
    
    @for ($i = 0; $i < $emptyStars; $i++)
        <svg class="w-4 h-4 text-gray-300 fill-current" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
        </svg>
    @endfor
</div>

