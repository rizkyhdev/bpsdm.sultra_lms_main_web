@extends('layouts.instructor')

@section('title','Modules')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $course) }}">{{ $course->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Modules</li>
  </ol>
  {{-- Binding: $course, $modules --}}
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
      <h4 class="mb-0">Modules</h4>
      <small class="text-muted">Course: {{ $course->judul }}</small>
    </div>
    <div>
      <a href="{{ route('instructor.courses.show', $course) }}" class="btn btn-light btn-sm me-2">
        <i class="bi bi-arrow-left"></i> Back to Course
      </a>
      @can('create', [App\Models\Module::class, $course])
        <a href="{{ route('instructor.modules.create', $course->id) }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-circle"></i> Add Module
        </a>
      @endcan
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Modules List ({{ $modules->total() }})</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th width="80">Order</th>
              <th>Title</th>
              <th>Description</th>
              <th width="120" class="text-center">Sub-Modules</th>
              <th width="200" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($modules as $m)
              <tr>
                <td class="text-center">
                  <span class="badge bg-secondary">{{ $m->urutan }}</span>
                </td>
                <td>
                  <strong>{{ $m->judul }}</strong>
                </td>
                <td>
                  <small class="text-muted">{{ Str::limit($m->deskripsi ?? 'No description', 100) }}</small>
                </td>
                <td class="text-center">
                  <span class="badge bg-info">{{ $m->sub_modules_count ?? 0 }}</span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('instructor.modules.show', $m) }}" class="btn btn-outline-primary" title="View">
                      <i class="bi bi-eye"></i>
                    </a>
                    @can('update', $m)
                      <a href="{{ route('instructor.modules.edit', $m) }}" class="btn btn-outline-secondary" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                    @endcan
                    @can('delete', $m)
                      <form action="{{ route('instructor.modules.destroy', $m) }}" method="post" class="d-inline" 
                            onsubmit="return confirm('Are you sure you want to delete this module?\n\nThis will also delete:\n- All sub-modules\n- All contents\n- All quizzes and questions\n\nThis action cannot be undone!');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    @endcan
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4">
                  <div class="text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2">No modules found. Create your first module!</p>
                    @can('create', [App\Models\Module::class, $course])
                      <a href="{{ route('instructor.modules.create', $course->id) }}" class="btn btn-primary btn-sm mt-2">
                        <i class="bi bi-plus-circle"></i> Add Module
                      </a>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($modules->hasPages())
      <div class="card-footer">
        {{ $modules->links() }}
      </div>
    @endif
  </div>
</div>
@endsection


