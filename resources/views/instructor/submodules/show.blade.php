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
        <a href="{{ route('instructor.sub_modules.edit', $subModule->id) }}" class="btn btn-outline-secondary btn-sm me-2">
          <i class="bi bi-pencil"></i> Edit
        </a>
      @endcan
      @can('delete', $subModule)
        <form action="{{ route('instructor.sub_modules.destroy', $subModule->id) }}" method="post" class="d-inline" 
              onsubmit="return confirm('Are you sure you want to delete this sub-module?\n\nThis will also delete:\n- All contents\n- All quizzes and questions\n\nThis action cannot be undone!');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm me-2">
            <i class="bi bi-trash"></i> Delete
          </button>
        </form>
      @endcan
    </div>
  </div>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#contents" role="tab">Contents</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#quizzes" role="tab">Quizzes</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#progress" role="tab">Progress</a></li>
  </ul>
  <div class="tab-content p-3 border border-top-0">
    <div class="tab-pane fade show active" id="contents" role="tabpanel">
      <div class="d-flex mb-2">
        @can('create', [App\Models\Content::class, $subModule])
          <a href="{{ route('instructor.contents.create', $subModule->id) }}" class="btn btn-primary btn-sm">Tambah Content</a>
        @endcan
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
                <i class="bi bi-eye"></i>
              </a>
              @can('update', $c)
                <a href="{{ route('instructor.contents.edit', $c->id) }}" class="btn btn-outline-secondary" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
              @endcan
              @can('delete', $c)
                <form action="{{ route('instructor.contents.destroy', $c->id) }}" method="post" class="d-inline" 
                      onsubmit="return confirm('Are you sure you want to delete this content?\n\nThis action cannot be undone!');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              @endcan
            </div>
          </div>
        @empty
          <div class="text-center py-4 text-muted">
            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
            <p class="mt-2">No contents found. Create your first content!</p>
            @can('create', [App\Models\Content::class, $subModule])
              <a href="{{ route('instructor.contents.create', $subModule->id) }}" class="btn btn-primary btn-sm mt-2">
                <i class="bi bi-plus-circle"></i> Add Content
              </a>
            @endcan
          </div>
        @endforelse
      </div>
    </div>
    <div class="tab-pane fade" id="quizzes" role="tabpanel">
      <div class="d-flex mb-2">
        @can('create', [App\Models\Quiz::class, $subModule])
          <a href="{{ route('instructor.quizzes.create', $subModule->id) }}" class="btn btn-primary btn-sm">Tambah Quiz</a>
        @endcan
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
                <i class="bi bi-eye"></i>
              </a>
              @can('update', $q)
                <a href="{{ route('instructor.quizzes.edit', $q->id) }}" class="btn btn-outline-secondary" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
              @endcan
              @can('delete', $q)
                <form action="{{ route('instructor.quizzes.destroy', $q->id) }}" method="post" class="d-inline" 
                      onsubmit="return confirm('Are you sure you want to delete this quiz?\n\nThis will also delete:\n- All questions\n- All answer options\n\nThis action cannot be undone!');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              @endcan
            </div>
          </div>
        @empty
          <div class="text-center py-4 text-muted">
            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
            <p class="mt-2">No quizzes found. Create your first quiz!</p>
            @can('create', [App\Models\Quiz::class, $subModule])
              <a href="{{ route('instructor.quizzes.create', $subModule->id) }}" class="btn btn-primary btn-sm mt-2">
                <i class="bi bi-plus-circle"></i> Add Quiz
              </a>
            @endcan
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
@endsection

