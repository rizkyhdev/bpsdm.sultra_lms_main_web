@extends('layouts.instructor')

@section('title', $module->judul)

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $module->course) }}">{{ $module->course->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $module->judul }}</li>
  </ol>
  {{-- Binding: $module, $subModules --}}
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
      <h4 class="mb-0">{{ $module->judul }}</h4>
      <small class="text-muted">{{ $module->deskripsi }}</small>
    </div>
    <div>
      <a href="{{ route('instructor.modules.index', $module->course_id) }}" class="btn btn-light btn-sm me-2">
        <i class="bi bi-arrow-left"></i> Back to Modules
      </a>
      @can('update', $module)
        <a href="{{ route('instructor.modules.edit', $module) }}" class="btn btn-outline-secondary btn-sm me-2">
          <i class="bi bi-pencil"></i> Edit
        </a>
      @endcan
      @can('delete', $module)
        <form action="{{ route('instructor.modules.destroy', $module) }}" method="post" class="d-inline" 
              onsubmit="return confirm('Are you sure you want to delete this module?\n\nThis will also delete:\n- All sub-modules\n- All contents\n- All quizzes and questions\n\nThis action cannot be undone!');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm me-2">
            <i class="bi bi-trash"></i> Delete
          </button>
        </form>
      @endcan
    @can('create', [App\Models\SubModule::class, $module])
        <a href="{{ route('instructor.sub_modules.create', $module->id) }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-circle"></i> Add Sub-Module
        </a>
    @endcan
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Sub-Modules</h5>
          <h2 class="mb-0">{{ $subModules->count() }}</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Sub-Modules</h5>
    </div>
    <div class="card-body">
      @forelse($subModules as $sm)
        <div class="card mb-2">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-1">
                  <span class="badge bg-secondary me-2">{{ $sm->urutan }}</span>
                  {{ $sm->judul }}
                </h6>
                @if($sm->deskripsi)
                  <small class="text-muted">{{ Str::limit($sm->deskripsi, 150) }}</small>
                @endif
              </div>
              <div>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="{{ route('instructor.sub_modules.show', $sm->id) }}" class="btn btn-outline-primary" title="View">
                    <i class="bi bi-eye"></i> View
                  </a>
                  @can('update', $sm)
                    <a href="{{ route('instructor.sub_modules.edit', $sm->id) }}" class="btn btn-outline-secondary" title="Edit">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                  @endcan
                  @can('delete', $sm)
                    <form action="{{ route('instructor.sub_modules.destroy', $sm->id) }}" method="post" class="d-inline" 
                          onsubmit="return confirm('Are you sure you want to delete this sub-module?\n\nThis will also delete:\n- All contents\n- All quizzes and questions\n\nThis action cannot be undone!');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger" title="Delete">
                        <i class="bi bi-trash"></i> Delete
                      </button>
                    </form>
                  @endcan
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="text-center py-4">
          <div class="text-muted">
            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
            <p class="mt-2">No sub-modules found. Create your first sub-module!</p>
            @can('create', [App\Models\SubModule::class, $module])
              <a href="{{ route('instructor.sub_modules.create', $module->id) }}" class="btn btn-primary btn-sm mt-2">
                <i class="bi bi-plus-circle"></i> Add Sub-Module
              </a>
            @endcan
          </div>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection


