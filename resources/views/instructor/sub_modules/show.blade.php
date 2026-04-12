@extends('layouts.instructor')

@section('title', $subModule->judul)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $subModule->module->course->id) }}">{{ $subModule->module->course->judul }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $subModule->module) }}">{{ $subModule->module->judul }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $subModule->judul }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Sub-Module Header -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="icon-shape bg-primary-soft text-primary rounded-3">
                        <i class="bi bi-folder2-open fs-3"></i>
                    </div>
                </div>
                <div class="col">
                    <h3 class="mb-1 fw-bold text-dark">{{ $subModule->judul }}</h3>
                    <p class="text-muted mb-0">{{ $subModule->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                </div>
                <div class="col-auto text-end">
                    <div class="badge bg-info-soft text-info rounded-pill px-3 py-2">
                        <i class="bi bi-people me-1"></i> {{ $progressSummary['participants'] ?? 0 }} Peserta
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Nav -->
    <ul class="nav nav-pills mb-4" id="subModuleTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-semibold px-4 rounded-pill me-2" id="contents-tab" data-bs-toggle="pill" data-bs-target="#contents" type="button" role="tab shadow-sm">
                <i class="bi bi-file-earmark-text me-2"></i>Contents
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-semibold px-4 rounded-pill me-2" id="quizzes-tab" data-bs-toggle="pill" data-bs-target="#quizzes" type="button" role="tab shadow-sm">
                <i class="bi bi-question-circle me-2"></i>Quizzes
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-semibold px-4 rounded-pill" id="progress-tab" data-bs-toggle="pill" data-bs-target="#progress" type="button" role="tab shadow-sm">
                <i class="bi bi-bar-chart-line me-2"></i>Progress
            </button>
        </li>
    </ul>

    <div class="tab-content border-0">
        <!-- Contents Tab -->
        <div class="tab-pane fade show active" id="contents" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-task text-primary me-2"></i>Daftar Konten</h5>
                @can('create', [App\Models\Content::class, $subModule])
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addContentModal">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Content
                    </button>
                @endcan
            </div>
            
            <div id="contents-list" class="sortable-list">
                @forelse($contents as $c)
                    <div class="card border-0 shadow-sm rounded-3 mb-3 content-item" data-id="{{ $c->id }}">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="drag-handle fs-4 text-muted me-3 cursor-grab">
                                    <i class="bi bi-grip-vertical"></i>
                                </div>
                                <div class="flex-shrink-0 me-3">
                                    <div class="icon-shape bg-light rounded-3 text-primary">
                                        @if($c->tipe == 'video' || $c->tipe == 'youtube') <i class="bi bi-play-btn fs-5"></i>
                                        @elseif($c->tipe == 'pdf') <i class="bi bi-file-earmark-pdf fs-5"></i>
                                        @elseif($c->tipe == 'image') <i class="bi bi-image fs-5"></i>
                                        @else <i class="bi bi-file-text fs-5"></i> @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold"><a href="{{ route('instructor.contents.show', $c) }}" class="text-dark text-decoration-none hov-primary">{{ $c->judul }}</a></h6>
                                    <small class="text-muted text-uppercase fw-semibold">{{ $c->tipe }}</small>
                                </div>
                                <div class="col-auto">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon border-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                            <li><a class="dropdown-menu-item dropdown-item py-2" href="{{ route('instructor.contents.show', $c) }}"><i class="bi bi-eye me-2"></i> Detail</a></li>
                                            <li><a class="dropdown-menu-item dropdown-item py-2" href="{{ route('instructor.contents.edit', $c) }}"><i class="bi bi-pencil me-2"></i> Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('instructor.contents.destroy', $c) }}" method="POST" onsubmit="return confirm('Hapus konten ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-trash me-2"></i> Hapus</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-5 text-center">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                            <h5 class="text-muted fw-normal">Belum ada konten di sub-modul ini.</h5>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quizzes Tab -->
        <div class="tab-pane fade" id="quizzes" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-patch-question text-primary me-2"></i>Daftar Kuis</h5>
                @can('create', [App\Models\Quiz::class, $subModule])
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addQuizModal">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Quiz
                    </button>
                @endcan
            </div>

            <div id="quizzes-list" class="sortable-list">
                @forelse($quizzes as $q)
                    <div class="card border-0 shadow-sm rounded-3 mb-3 quiz-item" data-id="{{ $q->id }}">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="drag-handle fs-4 text-muted me-3 cursor-grab">
                                    <i class="bi bi-grip-vertical"></i>
                                </div>
                                <div class="flex-shrink-0 me-3">
                                    <div class="icon-shape bg-warning-soft rounded-3 text-warning">
                                        <i class="bi bi-journal-check fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold"><a href="{{ route('instructor.quizzes.show', $q) }}" class="text-dark text-decoration-none hov-primary">{{ $q->judul }}</a></h6>
                                    <small class="text-muted fw-semibold">{{ $q->questions_count ?? 0 }} Pertanyaan • Passing: {{ $q->nilai_minimum }}</small>
                                </div>
                                <div class="col-auto">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon border-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                            <li><a class="dropdown-menu-item dropdown-item py-2" href="{{ route('instructor.quizzes.show', $q) }}"><i class="bi bi-eye me-2"></i> Detail</a></li>
                                            <li><a class="dropdown-menu-item dropdown-item py-2" href="{{ route('instructor.quizzes.edit', $q) }}"><i class="bi bi-pencil me-2"></i> Edit</a></li>
                                            <li><a class="dropdown-menu-item dropdown-item py-2" href="{{ route('instructor.quizzes.results', $q) }}"><i class="bi bi-clipboard2-data me-2"></i> Hasil</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('instructor.quizzes.destroy', $q) }}" method="POST" onsubmit="return confirm('Hapus kuis ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-trash me-2"></i> Hapus</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-5 text-center">
                            <i class="bi bi-mortarboard fs-1 text-muted d-block mb-3"></i>
                            <h5 class="text-muted fw-normal">Belum ada kuis di sub-modul ini.</h5>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Progress Tab -->
        <div class="tab-pane fade" id="progress" role="tabpanel">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-primary-soft text-primary me-3">
                                    <i class="bi bi-pie-chart"></i>
                                </div>
                                <h6 class="mb-0 text-muted fw-bold">Completion Rata-rata</h6>
                            </div>
                            <h2 class="fw-bold mb-0 text-dark">{{ $progressSummary['avg_completion'] ?? 0 }}%</h2>
                            <div class="progress mt-3 rounded-pill" style="height: 10px;">
                                <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $progressSummary['avg_completion'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-info-soft text-info me-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <h6 class="mb-0 text-muted fw-bold">Jumlah Peserta</h6>
                            </div>
                            <h2 class="fw-bold mb-0 text-dark">{{ $progressSummary['participants'] ?? 0 }}</h2>
                            <p class="text-muted small mt-2 mb-0">Peserta yang terdaftar aktif.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-success-soft text-success me-3">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <h6 class="mb-0 text-muted fw-bold">Selesai</h6>
                            </div>
                            <h2 class="fw-bold mb-0 text-dark">{{ $progressSummary['completed'] ?? 0 }}</h2>
                            <p class="text-muted small mt-2 mb-0">Peserta yang melampaui Passing Grade.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold">Statistik Detail akan segera hadir...</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Content Modal -->
<div class="modal fade" id="addContentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header py-3 border-bottom-0">
                <h5 class="modal-title fw-bold">Tambah Content Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('instructor.sub_modules.contents.store', $subModule) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 pt-1">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Judul Content <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control rounded-3" placeholder="Masukkan judul materi..." required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Tipe Content <span class="text-danger">*</span></label>
                            <select name="tipe" class="form-select rounded-3" id="contentTypeSelector" required>
                                <option value="" disabled selected>Pilih Tipe Content</option>
                                <option value="text">Text (Plain Text)</option>
                                <option value="html">HTML (Rich Text)</option>
                                <option value="video">Video (File)</option>
                                <option value="youtube">YouTube Video</option>
                                <option value="audio">Audio (File)</option>
                                <option value="pdf">PDF File</option>
                                <option value="image">Image File</option>
                                <option value="link">External Link</option>
                            </select>
                        </div>
                    </div>

                    <div id="contentFieldsContainer">
                        <!-- Extra fields injected via JS -->
                        <div class="mb-4 d-none field-group" id="htmlField">
                            <label class="form-label fw-semibold">Konten (Text/HTML) <span class="text-danger">*</span></label>
                            <textarea name="html_content" class="form-control rounded-3" rows="5"></textarea>
                        </div>

                        <div class="mb-4 d-none field-group" id="fileField">
                            <label class="form-label fw-semibold">Upload File <span class="text-danger">*</span></label>
                            <input type="file" name="file_path" class="form-control rounded-3">
                            <small class="text-muted mt-2 d-block">Maksimal ukuran file: 100MB.</small>
                        </div>

                        <div class="mb-4 d-none field-group" id="youtubeField">
                            <label class="form-label fw-semibold">YouTube URL <span class="text-danger">*</span></label>
                            <input type="url" name="youtube_url" class="form-control rounded-3" placeholder="https://www.youtube.com/watch?v=...">
                        </div>

                        <div class="mb-4 d-none field-group" id="linkField">
                            <label class="form-label fw-semibold">External Link <span class="text-danger">*</span></label>
                            <input type="url" name="external_url" class="form-control rounded-3" placeholder="https://example.com/file">
                        </div>

                        <div class="mb-4 d-none field-group" id="durationField">
                            <label class="form-label fw-semibold">Durasi Minimal (Detik)</label>
                            <input type="number" name="required_duration" class="form-control rounded-3" min="0" placeholder="Opsional">
                            <small class="text-muted">Lama waktu minimal siswa harus berada di konten ini.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 text-center d-grid">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm">Simpan Content</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Quiz Modal -->
<div class="modal fade" id="addQuizModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header py-3 border-bottom-0">
                <h5 class="modal-title fw-bold">Tambah Quiz Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('instructor.sub_modules.quizzes.store', $subModule) }}" method="POST">
                @csrf
                <div class="modal-body p-4 pt-1">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Judul Quiz <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control rounded-3" placeholder="Misal: Kuis Modul 1" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="3" placeholder="Petunjuk pengerjaan kuis..."></textarea>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Passing Grade</label>
                            <div class="input-group">
                                <input type="number" name="nilai_minimum" class="form-control rounded-3 @error('nilai_minimum') is-invalid @enderror" min="0" max="100" step="1" value="70" required>
                                <span class="input-group-text bg-white rounded-3 ms-1 border-0">%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Maksimal Percobaan</label>
                            <input type="number" name="max_attempts" class="form-control rounded-3 @error('max_attempts') is-invalid @enderror" min="1" value="3" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 text-center d-grid">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm">Simpan Quiz</button>
                    <small class="text-muted mt-2">Setelah disimpan, Anda dapat menambahkan pertanyaan pada halaman detail kuis.</small>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
    .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    
    .icon-shape {
        width: 54px;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .icon-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .rounded-4 { border-radius: 1rem !important; }
    .rounded-3 { border-radius: 0.75rem !important; }
    
    .cursor-grab { cursor: grab; }
    .cursor-grab:active { cursor: grabbing; }
    
    .nav-pills .nav-link {
        color: #6c757d;
        background: white;
    }
    .nav-pills .nav-link.active {
        color: #fff;
        background-color: #0d6efd;
    }
    
    .hov-primary:hover { color: #0d6efd !important; }
    
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .content-item:hover, .quiz-item:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        transition: all 0.2s;
    }

    /* Sortable placeholder */
    .sortable-ghost {
        opacity: 0.4;
        border: 2px dashed #0d6efd !important;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Content Reordering
    const contentListEl = document.getElementById('contents-list');
    if (contentListEl) {
        new Sortable(contentListEl, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                const items = [];
                contentListEl.querySelectorAll('.content-item').forEach((el, index) => {
                    items.push({
                        id: el.dataset.id,
                        urutan: index + 1
                    });
                });
                
                updateOrder('{{ route("instructor.contents.reorder") }}', items);
            }
        });
    }

    // Quiz Reordering
    const quizListEl = document.getElementById('quizzes-list');
    if (quizListEl) {
        new Sortable(quizListEl, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                const items = [];
                quizListEl.querySelectorAll('.quiz-item').forEach((el, index) => {
                    items.push({
                        id: el.dataset.id,
                        urutan: index + 1
                    });
                });
                
                updateOrder('{{ route("instructor.quizzes.reorder") }}', items);
            }
        });
    }

    function updateOrder(url, items) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ items: items })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Order updated:', data);
        })
        .catch(error => {
            console.error('Error updating order:', error);
            alert('Gagal mengubah urutan. Silakan refresh halaman.');
        });
    }

    // Modal Field Toggles
    const typeSelector = document.getElementById('contentTypeSelector');
    const container = document.getElementById('contentFieldsContainer');
    
    if (typeSelector) {
        typeSelector.addEventListener('change', function() {
            const type = this.value;
            // Reset state
            container.querySelectorAll('.field-group').forEach(group => group.classList.add('d-none'));
            
            if (type === 'text' || type === 'html') {
                document.getElementById('htmlField').classList.remove('d-none');
                document.getElementById('durationField').classList.remove('d-none');
            } else if (['video', 'audio', 'pdf', 'image'].includes(type)) {
                document.getElementById('fileField').classList.remove('d-none');
                document.getElementById('durationField').classList.remove('d-none');
            } else if (type === 'youtube') {
                document.getElementById('youtubeField').classList.remove('d-none');
                document.getElementById('durationField').classList.remove('d-none');
            } else if (type === 'link') {
                document.getElementById('linkField').classList.remove('d-none');
            }
        });
    }
});
</script>
@endpush
@endsection
