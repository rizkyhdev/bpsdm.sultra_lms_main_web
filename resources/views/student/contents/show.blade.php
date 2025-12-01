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
                            @if($content->youtube_embed_url && $content->youtube_video_id)
                                <div class="ratio ratio-16x9 mb-3">
                                    <div id="youtube-player"></div>
                                </div>
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Catatan:</strong> Video harus ditonton sampai selesai sebelum Anda dapat melanjutkan ke konten berikutnya.
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Error:</strong> URL YouTube tidak valid atau video tidak tersedia.
                                    @if($content->youtube_url)
                                        <br><small>URL yang dimasukkan: <a href="{{ $content->youtube_url }}" target="_blank">{{ $content->youtube_url }}</a></small>
                                    @endif
                                </div>
                            @endif
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
                        {{-- PDF Viewer (pdf.js) --}}
                        <div class="mb-4">
                            @php
                                $pdfUrl = Storage::url($content->file_path);
                            @endphp
                            @include('partials.pdf-viewer', [
                                'pdfUrl' => $pdfUrl,
                                'downloadUrl' => $pdfUrl,
                                'title' => $content->judul,
                            ])
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
                        {{-- External Link (with special handling for Google Drive PDF) --}}
                        <div class="mb-4">
                            @php
                                $externalUrl = $content->external_url;
                            @endphp

                            @if($externalUrl && \Illuminate\Support\Str::contains($externalUrl, 'drive.google.com'))
                                {{-- Google Drive PDF viewer --}}
                                @include('partials.google-drive-pdf-viewer', [
                                    'driveUrl' => $externalUrl,
                                    'title' => $content->judul,
                                ])
                            @else
                                {{-- Generic external link --}}
                                <div class="alert alert-primary">
                                    <i class="bi bi-link-45deg me-2"></i>
                                    <strong>Link Eksternal:</strong>
                                    <a href="{{ $externalUrl }}" target="_blank" class="alert-link">
                                        {{ $externalUrl }}
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </div>
                            @endif
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
                    @php
                        $canProceed = $progress->is_completed;
                        if ($content->required_duration) {
                            // Check if required duration has elapsed for any content type
                            $timeSpent = $progress->time_spent ?? 0;
                            $canProceed = $timeSpent >= $content->required_duration;
                        }
                    @endphp
                    <a href="{{ route('student.contents.show', $nextContent->id) }}" 
                       id="nextContentBtn"
                       class="btn btn-primary {{ !$canProceed ? 'disabled' : '' }}"
                       onclick="return checkCanProceed();">
                        Konten Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                    </a>
                    @if($content->required_duration && !$canProceed)
                        <div class="alert alert-warning mt-2 mb-0">
                            <small><i class="bi bi-clock me-1"></i>Anda harus menghabiskan waktu minimal {{ gmdate('H:i:s', $content->required_duration) }} sebelum dapat melanjutkan.</small>
                        </div>
                    @endif
                @else
                    {{-- No next content - show button to go back to sub-module --}}
                    @if($content->subModule)
                        <a href="{{ route('student.sub_modules.show', $content->sub_module_id) }}" 
                           class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Kembali ke Sub-Modul
                        </a>
                    @else
                        <button class="btn btn-primary" disabled>
                            Konten Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    @endif
                @endif
                
                {{-- Mark as Complete Button (shown if duration is not set and not completed) --}}
                @if(!$content->required_duration && !$progress->is_completed && $nextContent)
                    <button type="button" 
                            class="btn btn-success mt-2" 
                            id="markCompleteBtn"
                            onclick="markContentComplete()">
                        <i class="bi bi-check-circle me-1"></i>Tandai sebagai Selesai
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
                        
                        @if($content->required_duration)
                        <dt class="col-sm-5 text-muted small">Durasi Diperlukan</dt>
                        <dd class="col-sm-7 small">{{ gmdate('H:i:s', $content->required_duration) }}</dd>
                        
                        <dt class="col-sm-5 text-muted small">Waktu Dihabiskan</dt>
                        <dd class="col-sm-7 small">{{ gmdate('H:i:s', $progress->time_spent ?? 0) }}</dd>
                        @else
                        <dt class="col-sm-5 text-muted small">Durasi</dt>
                        <dd class="col-sm-7 small">
                            <span class="badge bg-info">Tidak ditentukan</span>
                            <br><small class="text-muted">Anda dapat langsung menandai sebagai selesai</small>
                        </dd>
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

<script>
// Global functions for all content types
function markContentComplete() {
    if (confirm('Apakah Anda yakin ingin menandai konten ini sebagai selesai?')) {
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
                // Update UI
                const progressBar = document.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.classList.remove('bg-warning');
                    progressBar.classList.add('bg-success');
                    progressBar.style.width = '100%';
                    progressBar.setAttribute('aria-valuenow', '100');
                }
                
                const progressText = document.querySelector('.small.text-muted');
                if (progressText) {
                    progressText.textContent = '100.0%';
                }
                
                const statusBadge = document.querySelector('.badge.bg-warning');
                if (statusBadge) {
                    statusBadge.classList.remove('bg-warning');
                    statusBadge.classList.add('bg-success');
                    statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Selesai';
                }
                
                // Hide mark complete button
                const markCompleteBtn = document.getElementById('markCompleteBtn');
                if (markCompleteBtn) {
                    markCompleteBtn.style.display = 'none';
                }
                
                // Enable next button
                const nextButton = document.getElementById('nextContentBtn');
                if (nextButton) {
                    nextButton.classList.remove('disabled');
                }
                
                // Reload page after a short delay to update all UI elements
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                alert('Terjadi kesalahan: ' + (data.message || 'Gagal menandai konten sebagai selesai'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menandai konten sebagai selesai.');
        });
    }
}

function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    
    if (hours > 0) {
        return String(hours).padStart(2, '0') + ':' + 
               String(minutes).padStart(2, '0') + ':' + 
               String(secs).padStart(2, '0');
    }
    return String(minutes).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
}

@if($content->tipe !== 'youtube' && $content->tipe !== 'video')
// Time tracking for non-video content types
let timeSpentGlobal = {{ $progress->time_spent ?? 0 }};
let startTime = null;
let timeTrackingInterval = null;
let requiredDuration = {{ $content->required_duration ?? 0 }};

function startTimeTracking() {
    if (timeTrackingInterval) {
        clearInterval(timeTrackingInterval);
    }
    
    if (!startTime) {
        startTime = Date.now();
    }
    
    // Track time every 30 seconds
    timeTrackingInterval = setInterval(function() {
        if (startTime) {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            timeSpentGlobal += elapsed;
            startTime = Date.now();
            
            // Update time spent on server
            updateTimeSpentForAllContent();
        }
    }, 30000);
}

function updateTimeSpentForAllContent() {
    fetch('{{ route("student.contents.track-progress", $content->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            progress_percentage: {{ $progress->progress_percentage ?? 0 }},
            time_spent: timeSpentGlobal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            timeSpentGlobal = data.time_spent || timeSpentGlobal;
            
            // Check required duration
            if (requiredDuration > 0) {
                checkRequiredDuration();
                
                // If duration requirement is met and content is marked as completed, update UI
                if (timeSpentGlobal >= requiredDuration && data.is_completed) {
                    markContentAsCompleted();
                }
            }
        }
    })
    .catch(error => {
        console.error('Error updating time spent:', error);
    });
}

function checkRequiredDuration() {
    if (requiredDuration > 0) {
        const remainingTime = requiredDuration - timeSpentGlobal;
        const nextButton = document.getElementById('nextContentBtn');
        
        if (nextButton) {
            if (timeSpentGlobal >= requiredDuration) {
                nextButton.classList.remove('disabled');
                const warningAlert = document.querySelector('.alert-warning');
                if (warningAlert) {
                    warningAlert.style.display = 'none';
                }
                
                // Automatically mark as completed when duration is met
                // Check if not already completed to avoid multiple calls
                const statusBadge = document.querySelector('.badge.bg-warning');
                if (statusBadge && statusBadge.classList.contains('bg-warning')) {
                    markContentAsCompleted();
                    
                    // If there's no next content, reload page to show sub-module button
                    const nextContentBtn = document.getElementById('nextContentBtn');
                    if (!nextContentBtn || nextContentBtn.disabled) {
                        // Check if this is the last content by checking if button text is "Konten Selanjutnya"
                        const nextButton = document.querySelector('a[href*="student.contents.show"]');
                        if (!nextButton) {
                            // No next content, reload to show sub-module button
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    }
                }
            } else {
                nextButton.classList.add('disabled');
                const warningAlert = document.querySelector('.alert-warning');
                if (warningAlert) {
                    const remainingFormatted = formatTime(remainingTime);
                    warningAlert.querySelector('small').innerHTML = 
                        '<i class="bi bi-clock me-1"></i>Anda harus menghabiskan waktu minimal ' + 
                        formatTime(requiredDuration) + ' sebelum dapat melanjutkan. Waktu tersisa: ' + remainingFormatted;
                }
            }
        }
    }
}

function markContentAsCompleted() {
    // Update UI to show content is completed
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        progressBar.classList.remove('bg-warning');
        progressBar.classList.add('bg-success');
        progressBar.style.width = '100%';
        progressBar.setAttribute('aria-valuenow', '100');
    }
    
    const progressText = document.querySelector('.small.text-muted');
    if (progressText) {
        progressText.textContent = '100.0%';
    }
    
    const statusBadge = document.querySelector('.badge.bg-warning');
    if (statusBadge) {
        statusBadge.classList.remove('bg-warning');
        statusBadge.classList.add('bg-success');
        statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Selesai';
    }
    
    // Hide mark complete button if it exists
    const markCompleteBtn = document.getElementById('markCompleteBtn');
    if (markCompleteBtn) {
        markCompleteBtn.style.display = 'none';
    }
    
    // Enable next button
    const nextButton = document.getElementById('nextContentBtn');
    if (nextButton) {
        nextButton.classList.remove('disabled');
    }
    
    // Hide warning alert if it exists
    const warningAlert = document.querySelector('.alert-warning');
    if (warningAlert) {
        warningAlert.style.display = 'none';
    }
}

// Stop time tracking when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        if (timeTrackingInterval) {
            clearInterval(timeTrackingInterval);
            timeTrackingInterval = null;
        }
        if (startTime) {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            timeSpentGlobal += elapsed;
            startTime = null;
            updateTimeSpentForAllContent();
        }
    } else {
        startTimeTracking();
    }
});

// Start tracking when page loads
document.addEventListener('DOMContentLoaded', function() {
    startTimeTracking();
    if (requiredDuration > 0) {
        checkRequiredDuration();
    }
});
</script>
@endif

@if($content->tipe === 'youtube' && $content->youtube_embed_url)
<script src="https://www.youtube.com/iframe_api"></script>
<script>
let player;
let progressTrackingInterval;
let lastTrackedPosition = {{ $progress->current_position ?? 0 }};
let videoDuration = {{ $progress->video_duration ?? 0 }};
let watchedDuration = {{ $progress->watched_duration ?? 0 }};
let timeSpent = {{ $progress->time_spent ?? 0 }};
let isCompleted = {{ $progress->is_completed ? 'true' : 'false' }};
let maxSeekPosition = lastTrackedPosition; // Prevent skipping ahead
let videoId = '{{ $content->youtube_video_id }}';
let requiredDuration = {{ $content->required_duration ?? 0 }};
let startTime = null;
let isPlaying = false;
let playStartTime = null;
let timeSpentGlobal = {{ $progress->time_spent ?? 0 }};
let timeTrackingInterval = null;

function onYouTubeIframeAPIReady() {
    if (!videoId) {
        console.error('YouTube video ID tidak ditemukan');
        return;
    }
    
    try {
        player = new YT.Player('youtube-player', {
            videoId: videoId,
            playerVars: {
                'enablejsapi': 1,
                'origin': window.location.origin,
                'rel': 0,
                'modestbranding': 1,
                'controls': 1
            },
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange,
                'onError': onPlayerError
            }
        });
    } catch (error) {
        console.error('Error initializing YouTube player:', error);
    }
}

function onPlayerError(event) {
    console.error('YouTube player error:', event.data);
    if (event.data === 100 || event.data === 101 || event.data === 150) {
        alert('Video tidak tersedia atau tidak dapat diputar. Silakan coba lagi atau hubungi administrator.');
    }
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
    
    // Initialize start time
    if (!startTime) {
        startTime = Date.now();
    }
    
    // Check required duration on load
    checkRequiredDuration();
    
    // Start tracking progress
    startProgressTracking();
}

function onPlayerStateChange(event) {
    if (event.data === YT.PlayerState.PLAYING) {
        if (!isPlaying) {
            isPlaying = true;
            playStartTime = Date.now();
            if (!startTime) {
                startTime = Date.now();
            }
        }
        startProgressTracking();
    } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
        if (isPlaying && playStartTime) {
            // Add time spent while playing
            const playDuration = Math.floor((Date.now() - playStartTime) / 1000);
            timeSpent += playDuration;
            playStartTime = null;
        }
        isPlaying = false;
        stopProgressTracking();
        
        if (event.data === YT.PlayerState.ENDED) {
            // Video completed
            markVideoComplete();
        }
        
        // Update time spent on server
        updateTimeSpent();
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
            
            // Check required duration periodically
            checkRequiredDuration();
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
    // Update time spent if playing
    if (isPlaying && playStartTime) {
        const playDuration = Math.floor((Date.now() - playStartTime) / 1000);
        const currentTimeSpent = timeSpent + playDuration;
        
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
                time_spent: currentTimeSpent
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                timeSpent = data.time_spent || currentTimeSpent;
                
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
                
                // Check if required duration has elapsed
                checkRequiredDuration();
                
                // If completed, allow navigation
                if (data.is_completed) {
                    isCompleted = true;
                    enableNextButton();
                    markContentAsCompletedForVideo();
                }
            }
        })
        .catch(error => {
            console.error('Error tracking progress:', error);
        });
    }
}

function updateTimeSpent() {
    if (timeSpent > 0) {
        fetch('{{ route("student.contents.track-progress", $content->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                progress_percentage: {{ $progress->progress_percentage ?? 0 }},
                current_position: lastTrackedPosition,
                video_duration: videoDuration,
                watched_duration: watchedDuration,
                time_spent: timeSpent
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                timeSpent = data.time_spent || timeSpent;
                checkRequiredDuration();
            }
        });
    }
}

function checkRequiredDuration() {
    if (requiredDuration > 0) {
        const remainingTime = requiredDuration - timeSpent;
        const nextButton = document.getElementById('nextContentBtn');
        
        if (nextButton) {
            if (timeSpent >= requiredDuration) {
                nextButton.classList.remove('disabled');
                const warningAlert = document.querySelector('.alert-warning');
                if (warningAlert) {
                    warningAlert.style.display = 'none';
                }
                
                // Automatically mark as completed when duration is met
                if (!isCompleted) {
                    markContentAsCompletedForVideo();
                }
            } else {
                nextButton.classList.add('disabled');
                const warningAlert = document.querySelector('.alert-warning');
                if (warningAlert) {
                    const remainingFormatted = formatTime(remainingTime);
                    warningAlert.querySelector('small').innerHTML = 
                        '<i class="bi bi-clock me-1"></i>Anda harus menghabiskan waktu minimal ' + 
                        formatTime(requiredDuration) + ' sebelum dapat melanjutkan. Waktu tersisa: ' + remainingFormatted;
                }
            }
        }
    }
}

function markContentAsCompletedForVideo() {
    // Update UI to show content is completed
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        progressBar.classList.remove('bg-warning');
        progressBar.classList.add('bg-success');
        progressBar.style.width = '100%';
        progressBar.setAttribute('aria-valuenow', '100');
    }
    
    const progressText = document.querySelector('.small.text-muted');
    if (progressText) {
        progressText.textContent = '100.0%';
    }
    
    const statusBadge = document.querySelector('.badge.bg-warning');
    if (statusBadge) {
        statusBadge.classList.remove('bg-warning');
        statusBadge.classList.add('bg-success');
        statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Selesai';
    }
    
    // Enable next button
    const nextButton = document.getElementById('nextContentBtn');
    if (nextButton) {
        nextButton.classList.remove('disabled');
    }
    
    // Hide warning alert if it exists
    const warningAlert = document.querySelector('.alert-warning');
    if (warningAlert) {
        warningAlert.style.display = 'none';
    }
    
    isCompleted = true;
}

// Start time tracking for all content types when page loads
function startTimeTracking() {
    if (timeTrackingInterval) {
        clearInterval(timeTrackingInterval);
    }
    
    if (!startTime) {
        startTime = Date.now();
    }
    
    // Track time every 30 seconds for all content types
    timeTrackingInterval = setInterval(function() {
        if (startTime) {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            timeSpentGlobal += elapsed;
            startTime = Date.now();
            
            // Update time spent on server
            updateTimeSpentForAllContent();
        }
    }, 30000); // Track every 30 seconds
}

function updateTimeSpentForAllContent() {
    fetch('{{ route("student.contents.track-progress", $content->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            progress_percentage: {{ $progress->progress_percentage ?? 0 }},
            time_spent: timeSpentGlobal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            timeSpentGlobal = data.time_spent || timeSpentGlobal;
            
            // Check required duration
            if (requiredDuration > 0) {
                checkRequiredDuration();
            }
        }
    })
    .catch(error => {
        console.error('Error updating time spent:', error);
    });
}

// Stop time tracking when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        if (timeTrackingInterval) {
            clearInterval(timeTrackingInterval);
            timeTrackingInterval = null;
        }
        if (startTime) {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            timeSpentGlobal += elapsed;
            startTime = null;
            updateTimeSpentForAllContent();
        }
    } else {
        startTimeTracking();
    }
});

// Start tracking when page loads
document.addEventListener('DOMContentLoaded', function() {
    startTimeTracking();
});

function enableNextButton() {
    const nextButton = document.getElementById('nextContentBtn');
    if (nextButton) {
        nextButton.classList.remove('disabled');
    }
}

function checkCanProceed() {
    const requiredDuration = {{ $content->required_duration ?? 0 }};
    const timeSpent = {{ $progress->time_spent ?? 0 }};
    const isCompleted = {{ $progress->is_completed ? 'true' : 'false' }};
    
    if (requiredDuration > 0 && timeSpent < requiredDuration) {
        const remainingTime = requiredDuration - timeSpent;
        alert('Anda harus menghabiskan waktu minimal ' + formatTime(requiredDuration) + 
              ' sebelum dapat melanjutkan. Waktu tersisa: ' + formatTime(remainingTime));
        return false;
    }
    if (!isCompleted && requiredDuration > 0) {
        alert('Anda harus menyelesaikan konten ini terlebih dahulu!');
        return false;
    }
    return true;
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

