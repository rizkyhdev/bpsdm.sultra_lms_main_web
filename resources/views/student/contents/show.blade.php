@extends('layouts.studentapp')

@section('title', $content->judul)

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dasbor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">Pelatihan Saya</a></li>
            @if($content->subModule->module->course)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.courses.show', $content->subModule->module->course_id) }}">
                        {{ $content->subModule->module->course->judul }}
                    </a>
                </li>
            @endif
            @if($content->subModule->module)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.modules.show', $content->subModule->module_id) }}">
                        {{ $content->subModule->module->judul }}
                    </a>
                </li>
            @endif
            @if($content->subModule)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.sub_modules.show', $content->sub_module_id) }}">
                        {{ $content->subModule->judul }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ $content->judul }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-12 col-lg-8">
            {{-- Content Header --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body">
                    <h1 class="h3 fw-bold mb-3">{{ $content->judul }}</h1>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-primary">
                            @if($content->tipe === 'youtube')
                                <i class="bi bi-youtube me-1"></i>YouTube Video
                            @elseif($content->tipe === 'video')
                                <i class="bi bi-play-circle me-1"></i>Video
                            @elseif($content->tipe === 'html' || $content->tipe === 'text')
                                <i class="bi bi-file-text me-1"></i>Text
                            @elseif($content->tipe === 'pdf')
                                <i class="bi bi-file-pdf me-1"></i>PDF
                            @elseif($content->tipe === 'audio')
                                <i class="bi bi-music-note me-1"></i>Audio
                            @elseif($content->tipe === 'image')
                                <i class="bi bi-image me-1"></i>Image
                            @elseif($content->tipe === 'link')
                                <i class="bi bi-link-45deg me-1"></i>Link
                            @endif
                        </span>
                        @if($progress->is_completed)
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>Selesai
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="bi bi-clock me-1"></i>Berlangsung
                            </span>
                        @endif
                    </div>

                    {{-- Progress Bar --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-dark">Progress</span>
                            <span class="small text-muted">{{ number_format($progress->progress_percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar {{ $progress->is_completed ? 'bg-success' : 'bg-warning' }}" 
                                 role="progressbar" 
                                 style="width: {{ $progress->progress_percentage }}%; border-radius: 10px;" 
                                 aria-valuenow="{{ $progress->progress_percentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content Body --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body">
                    @if($content->tipe === 'youtube')
                        {{-- YouTube Video Player --}}
                        <div id="youtube-player-container" class="mb-4">
                            <div class="ratio ratio-16x9 mb-3">
                                <iframe id="youtube-player" 
                                        src="{{ $content->youtube_embed_url }}&controls=0&modestbranding=1&rel=0" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            </div>
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Catatan:</strong> Video harus ditonton sampai selesai sebelum Anda dapat melanjutkan ke konten berikutnya.
                            </div>
                        </div>
                    @elseif($content->tipe === 'video')
                        {{-- Regular Video Player --}}
                        <div class="mb-4">
                            <video id="video-player" class="w-100" controls style="max-height: 500px;">
                                <source src="{{ Storage::url($content->file_path) }}" type="video/mp4">
                                Browser Anda tidak mendukung video player.
                            </video>
                        </div>
                    @elseif($content->tipe === 'html' || $content->tipe === 'text')
                        {{-- HTML/Text Content --}}
                        <div class="content-body">
                            {!! $content->html_content ?? nl2br(e($content->html_content)) !!}
                        </div>
                    @elseif($content->tipe === 'pdf')
                        {{-- PDF Viewer --}}
                        <div class="mb-4">
                            <iframe src="{{ Storage::url($content->file_path) }}" 
                                    class="w-100" 
                                    style="height: 600px; border: 1px solid #ddd; border-radius: 8px;">
                            </iframe>
                        </div>
                    @elseif($content->tipe === 'audio')
                        {{-- Audio Player --}}
                        <div class="mb-4">
                            <audio id="audio-player" class="w-100" controls>
                                <source src="{{ Storage::url($content->file_path) }}" type="audio/mpeg">
                                Browser Anda tidak mendukung audio player.
                            </audio>
                        </div>
                    @elseif($content->tipe === 'image')
                        {{-- Image Display --}}
                        <div class="mb-4 text-center">
                            <img src="{{ Storage::url($content->file_path) }}" 
                                 alt="{{ $content->judul }}" 
                                 class="img-fluid rounded" 
                                 style="max-height: 600px;">
                        </div>
                    @elseif($content->tipe === 'link')
                        {{-- External Link --}}
                        <div class="mb-4">
                            <div class="alert alert-primary">
                                <i class="bi bi-link-45deg me-2"></i>
                                <strong>Link Eksternal:</strong>
                                <a href="{{ $content->external_url }}" target="_blank" class="alert-link">
                                    {{ $content->external_url }}
                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Navigation --}}
            <div class="d-flex justify-content-between">
                @if($previousContent)
                    <a href="{{ route('student.contents.show', $previousContent->id) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-chevron-left me-1"></i>Konten Sebelumnya
                    </a>
                @else
                    <button class="btn btn-outline-secondary" disabled>
                        <i class="bi bi-chevron-left me-1"></i>Konten Sebelumnya
                    </button>
                @endif

                @if($nextContent)
                    <a href="{{ route('student.contents.show', $nextContent->id) }}" 
                       class="btn btn-primary {{ !$progress->is_completed ? 'disabled' : '' }}"
                       onclick="return {{ $progress->is_completed ? 'true' : 'alert(\'Anda harus menyelesaikan konten ini terlebih dahulu!\'); return false;' }}">
                        Konten Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                    </a>
                @else
                    <button class="btn btn-primary" disabled>
                        Konten Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                    </button>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-lg-4">
            {{-- Content Info --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Informasi Konten</h5>
                </div>
                <div class="card-body">
                    <dl class="row g-3 mb-0">
                        <dt class="col-sm-5 text-muted small">Tipe</dt>
                        <dd class="col-sm-7 small">
                            <span class="badge bg-primary">
                                {{ ucfirst($content->tipe) }}
                            </span>
                        </dd>
                        
                        <dt class="col-sm-5 text-muted small">Progress</dt>
                        <dd class="col-sm-7 small">{{ number_format($progress->progress_percentage, 1) }}%</dd>
                        
                        <dt class="col-sm-5 text-muted small">Status</dt>
                        <dd class="col-sm-7 small">
                            @if($progress->is_completed)
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-warning">Berlangsung</span>
                            @endif
                        </dd>
                        
                        @if($progress->started_at)
                        <dt class="col-sm-5 text-muted small">Dimulai</dt>
                        <dd class="col-sm-7 small">{{ $progress->started_at->diffForHumans() }}</dd>
                        @endif
                        
                        @if($progress->completed_at)
                        <dt class="col-sm-5 text-muted small">Selesai</dt>
                        <dd class="col-sm-7 small">{{ $progress->completed_at->diffForHumans() }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Navigasi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($content->subModule)
                            <a href="{{ route('student.sub_modules.show', $content->sub_module_id) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Sub-Modul
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($content->tipe === 'youtube')
<script src="https://www.youtube.com/iframe_api"></script>
<script>
let player;
let progressTrackingInterval;
let lastTrackedPosition = {{ $progress->current_position ?? 0 }};
let videoDuration = {{ $progress->video_duration ?? 0 }};
let watchedDuration = {{ $progress->watched_duration ?? 0 }};
let isCompleted = {{ $progress->is_completed ? 'true' : 'false' }};
let maxSeekPosition = lastTrackedPosition; // Prevent skipping ahead

function onYouTubeIframeAPIReady() {
    player = new YT.Player('youtube-player', {
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
        }
    });
}

function onPlayerReady(event) {
    // Get video duration
    if (videoDuration === 0) {
        videoDuration = player.getDuration();
        updateVideoDuration(videoDuration);
    }
    
    // Seek to last position if video was not completed
    if (!isCompleted && lastTrackedPosition > 0) {
        player.seekTo(lastTrackedPosition, true);
    }
    
    // Start tracking progress
    startProgressTracking();
}

function onPlayerStateChange(event) {
    if (event.data === YT.PlayerState.PLAYING) {
        startProgressTracking();
    } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
        stopProgressTracking();
        
        if (event.data === YT.PlayerState.ENDED) {
            // Video completed
            markVideoComplete();
        }
    }
}

function startProgressTracking() {
    if (progressTrackingInterval) {
        clearInterval(progressTrackingInterval);
    }
    
    progressTrackingInterval = setInterval(function() {
        if (player && player.getCurrentTime) {
            const currentTime = player.getCurrentTime();
            const duration = player.getDuration();
            
            // Prevent skipping ahead - only allow seeking to already watched positions
            if (currentTime > maxSeekPosition + 5) {
                // User tried to skip ahead, seek back to max allowed position
                player.seekTo(maxSeekPosition, true);
                alert('Anda tidak dapat melewati bagian video yang belum ditonton. Silakan tonton video secara berurutan.');
                return;
            }
            
            // Update max seek position if we've watched further
            if (currentTime > maxSeekPosition) {
                maxSeekPosition = currentTime;
            }
            
            // Calculate progress
            const progressPercentage = duration > 0 ? (currentTime / duration) * 100 : 0;
            const watchedDuration = currentTime;
            
            // Track progress
            trackProgress(progressPercentage, currentTime, duration, watchedDuration);
            
            // Update last tracked position
            lastTrackedPosition = currentTime;
        }
    }, 5000); // Track every 5 seconds
}

function stopProgressTracking() {
    if (progressTrackingInterval) {
        clearInterval(progressTrackingInterval);
        progressTrackingInterval = null;
    }
}

function trackProgress(progressPercentage, currentPosition, duration, watchedDuration) {
    fetch('{{ route("student.contents.track-progress", $content->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            progress_percentage: progressPercentage,
            current_position: currentPosition,
            video_duration: duration,
            watched_duration: watchedDuration,
            time_spent: watchedDuration
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update progress bar
            const progressBar = document.querySelector('.progress-bar');
            const progressText = document.querySelector('.small.text-muted');
            if (progressBar) {
                progressBar.style.width = data.progress_percentage + '%';
                progressBar.setAttribute('aria-valuenow', data.progress_percentage);
            }
            if (progressText) {
                progressText.textContent = data.progress_percentage.toFixed(1) + '%';
            }
            
            // If completed, allow navigation
            if (data.is_completed) {
                isCompleted = true;
                const nextButton = document.querySelector('.btn-primary');
                if (nextButton && nextButton.classList.contains('disabled')) {
                    nextButton.classList.remove('disabled');
                    nextButton.onclick = null;
                }
            }
        }
    })
    .catch(error => {
        console.error('Error tracking progress:', error);
    });
}

function updateVideoDuration(duration) {
    fetch('{{ route("student.contents.track-progress", $content->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            progress_percentage: {{ $progress->progress_percentage ?? 0 }},
            current_position: lastTrackedPosition,
            video_duration: duration,
            watched_duration: watchedDuration
        })
    });
}

function markVideoComplete() {
    fetch('{{ route("student.contents.mark-complete", $content->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            isCompleted = true;
            // Update UI
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.classList.remove('bg-warning');
                progressBar.classList.add('bg-success');
                progressBar.style.width = '100%';
            }
            
            const statusBadge = document.querySelector('.badge.bg-warning');
            if (statusBadge) {
                statusBadge.classList.remove('bg-warning');
                statusBadge.classList.add('bg-success');
                statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Selesai';
            }
            
            // Enable next button
            const nextButton = document.querySelector('.btn-primary');
            if (nextButton && nextButton.classList.contains('disabled')) {
                nextButton.classList.remove('disabled');
                nextButton.onclick = null;
            }
        }
    });
}

// Prevent seeking ahead by intercepting seek events
document.addEventListener('DOMContentLoaded', function() {
    // This will be handled by YouTube API events
});
</script>
@elseif($content->tipe === 'video')
<script>
const videoPlayer = document.getElementById('video-player');
let progressTrackingInterval;

videoPlayer.addEventListener('loadedmetadata', function() {
    const duration = videoPlayer.duration;
    const currentTime = {{ $progress->current_position ?? 0 }};
    
    // Seek to last position if not completed
    if (!{{ $progress->is_completed ? 'true' : 'false' }} && currentTime > 0) {
        videoPlayer.currentTime = currentTime;
    }
    
    // Prevent skipping ahead
    let maxSeekPosition = currentTime;
    
    videoPlayer.addEventListener('seeked', function() {
        if (videoPlayer.currentTime > maxSeekPosition + 5) {
            videoPlayer.currentTime = maxSeekPosition;
            alert('Anda tidak dapat melewati bagian video yang belum ditonton.');
        }
    });
    
    videoPlayer.addEventListener('timeupdate', function() {
        if (videoPlayer.currentTime > maxSeekPosition) {
            maxSeekPosition = videoPlayer.currentTime;
        }
    });
    
    startProgressTracking();
});

videoPlayer.addEventListener('play', startProgressTracking);
videoPlayer.addEventListener('pause', stopProgressTracking);
videoPlayer.addEventListener('ended', function() {
    stopProgressTracking();
    markVideoComplete();
});

function startProgressTracking() {
    if (progressTrackingInterval) clearInterval(progressTrackingInterval);
    
    progressTrackingInterval = setInterval(function() {
        const currentTime = videoPlayer.currentTime;
        const duration = videoPlayer.duration;
        const progressPercentage = duration > 0 ? (currentTime / duration) * 100 : 0;
        
        trackProgress(progressPercentage, currentTime, duration, currentTime);
    }, 5000);
}

function stopProgressTracking() {
    if (progressTrackingInterval) {
        clearInterval(progressTrackingInterval);
        progressTrackingInterval = null;
    }
}

function trackProgress(progressPercentage, currentPosition, duration, watchedDuration) {
    fetch('{{ route("student.contents.track-progress", $content->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            progress_percentage: progressPercentage,
            current_position: currentPosition,
            video_duration: duration,
            watched_duration: watchedDuration,
            time_spent: watchedDuration
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const progressBar = document.querySelector('.progress-bar');
            const progressText = document.querySelector('.small.text-muted');
            if (progressBar) {
                progressBar.style.width = data.progress_percentage + '%';
            }
            if (progressText) {
                progressText.textContent = data.progress_percentage.toFixed(1) + '%';
            }
        }
    });
}

function markVideoComplete() {
    fetch('{{ route("student.contents.mark-complete", $content->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.classList.remove('bg-warning');
                progressBar.classList.add('bg-success');
                progressBar.style.width = '100%';
            }
        }
    });
}
</script>
@endif
@endsection

