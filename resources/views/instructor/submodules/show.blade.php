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
  {{-- Binding: $subModule, $contents, $quizzes, $progressSummary --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error!</strong> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">{{ $subModule->judul }}</h4>
      <small class="text-muted">{{ $subModule->deskripsi }}</small>
    </div>
    <div>
      <a href="{{ route('instructor.modules.show', $subModule->module_id) }}" class="btn btn-light btn-sm me-2">
        <i class="bi bi-arrow-left"></i> Back to Module
      </a>
      @can('update', $subModule)
        <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="openSubModuleModal({{ $subModule->module_id }}, {{ $subModule->id }})">
          <i class="bi bi-pencil"></i> Edit
        </button>
      @endcan
      @can('delete', $subModule)
        <button type="button" class="btn btn-outline-danger btn-sm me-2" onclick="openSubModuleDeleteModal({{ $subModule->id }})">
          <i class="bi bi-trash"></i> Delete
        </button>
      @endcan
    </div>
  </div>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#contents" role="tab">Contents</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#quizzes" role="tab">Quizzes</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#progress" role="tab">Progress</a></li>
  </ul>
  <div class="tab-content p-3 border border-top-0">
    <div class="tab-pane fade show active" id="contents" role="tabpanel">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">Contents</h6>
        <button type="button" class="btn btn-primary btn-sm" onclick="openContentModal({{ $subModule->id }})">
          <i class="bi bi-plus-circle"></i> Add Content
        </button>
      </div>
      <div class="list-group">
        @forelse($contents as $c)
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <span class="badge bg-secondary me-2">{{ $c->urutan }}</span>
              <strong>{{ $c->judul }}</strong>
              <span class="badge bg-info ms-2">{{ $c->tipe }}</span>
            </div>
            <div class="btn-group btn-group-sm" role="group">
              <a href="{{ route('instructor.contents.show', $c->id) }}" class="btn btn-outline-primary" title="View">
                <i class="bi bi-eye"></i> <span class="d-none d-md-inline">View</span>
              </a>
              <button type="button" class="btn btn-outline-secondary" onclick="openContentModal({{ $subModule->id }}, {{ $c->id }})" title="Edit">
                <i class="bi bi-pencil"></i> <span class="d-none d-md-inline">Edit</span>
              </button>
              <button type="button" class="btn btn-outline-danger" onclick="openContentDeleteModal({{ $c->id }})" title="Delete">
                <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Delete</span>
              </button>
            </div>
          </div>
        @empty
          <div class="text-center py-4 text-muted">
            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
            <p class="mt-2">No contents found. Create your first content!</p>
            <button type="button" class="btn btn-primary btn-sm mt-2" onclick="openContentModal({{ $subModule->id }})">
              <i class="bi bi-plus-circle"></i> Add Content
            </button>
          </div>
        @endforelse
      </div>
    </div>
    <div class="tab-pane fade" id="quizzes" role="tabpanel">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">Quizzes</h6>
        <button type="button" class="btn btn-primary btn-sm" onclick="openQuizModal({{ $subModule->id }})">
          <i class="bi bi-plus-circle"></i> Add Quiz
        </button>
      </div>
      <div class="list-group">
        @forelse($quizzes as $q)
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong>{{ $q->judul }}</strong>
              <small class="text-muted d-block">Min Score: {{ $q->nilai_minimum }}% | Max Attempts: {{ $q->max_attempts }}</small>
            </div>
            <div class="btn-group btn-group-sm" role="group">
              <a href="{{ route('instructor.quizzes.show', $q->id) }}" class="btn btn-outline-primary" title="View">
                <i class="bi bi-eye"></i> <span class="d-none d-md-inline">View</span>
              </a>
              <button type="button" class="btn btn-outline-secondary" onclick="openQuizModal({{ $subModule->id }}, {{ $q->id }})" title="Edit">
                <i class="bi bi-pencil"></i> <span class="d-none d-md-inline">Edit</span>
              </button>
              <button type="button" class="btn btn-outline-danger" onclick="openQuizDeleteModal({{ $q->id }})" title="Delete">
                <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Delete</span>
              </button>
            </div>
          </div>
        @empty
          <div class="text-center py-4 text-muted">
            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
            <p class="mt-2">No quizzes found. Create your first quiz!</p>
            <button type="button" class="btn btn-primary btn-sm mt-2" onclick="openQuizModal({{ $subModule->id }})">
              <i class="bi bi-plus-circle"></i> Add Quiz
            </button>
          </div>
        @endforelse
      </div>
    </div>
    <div class="tab-pane fade" id="progress" role="tabpanel">
      <div class="row">
        <div class="col-md-4">
          <div class="card"><div class="card-body">
            <div class="text-muted small">Completion Rata-rata</div>
            <h4>{{ $progressSummary['avg_completion'] ?? 0 }}%</h4>
          </div></div>
        </div>
        <div class="col-md-4">
          <div class="card"><div class="card-body">
            <div class="text-muted small">Jumlah Peserta</div>
            <h4>{{ $progressSummary['participants'] ?? 0 }}</h4>
          </div></div>
        </div>
        <div class="col-md-4">
          <div class="card"><div class="card-body">
            <div class="text-muted small">Selesai</div>
            <h4>{{ $progressSummary['completed'] ?? 0 }}</h4>
          </div></div>
        </div>
      </div>
    </div>
  </div>
</div>

@include('partials.modals.submodule-modal')
@include('partials.modals.content-modal')
@include('partials.modals.quiz-modal')
<script src="{{ asset('js/modal-operations.js') }}"></script>
@endsection

