@extends('layouts.instructor')

@section('title','Sub-Modules')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $module->course->id) }}">{{ $module->course->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $module) }}">{{ $module->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sub-Modules</li>
  </ol>
  {{-- Binding: $module, $subs --}}
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
      <h4 class="mb-0">Sub-Modules</h4>
      <small class="text-muted">Module: {{ $module->judul }}</small>
    </div>
    <div>
      <a href="{{ route('instructor.modules.show', $module->id) }}" class="btn btn-light btn-sm me-2">
        <i class="bi bi-arrow-left"></i> Back to Module
      </a>
      @can('create', [App\Models\SubModule::class, $module])
        <a href="{{ route('instructor.sub_modules.create', $module->id) }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-circle"></i> Add Sub-Module
        </a>
      @endcan
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Sub-Modules List ({{ $subs->total() }})</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th width="80">Order</th>
              <th>Title</th>
              <th>Description</th>
              <th width="120" class="text-center">Contents</th>
              <th width="120" class="text-center">Quizzes</th>
              <th width="200" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($subs as $sm)
              <tr>
                <td class="text-center">
                  <span class="badge bg-secondary">{{ $sm->urutan }}</span>
                </td>
                <td>
                  <strong>{{ $sm->judul }}</strong>
                </td>
                <td>
                  <small class="text-muted">{{ Str::limit($sm->deskripsi ?? 'No description', 100) }}</small>
                </td>
                <td class="text-center">
                  <span class="badge bg-info">{{ $sm->contents_count ?? 0 }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-warning">{{ $sm->quizzes_count ?? 0 }}</span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('instructor.sub_modules.show', $sm->id) }}" class="btn btn-outline-primary" title="View">
                      <i class="bi bi-eye"></i>
                    </a>
                    @can('update', $sm)
                      <a href="{{ route('instructor.sub_modules.edit', $sm->id) }}" class="btn btn-outline-secondary" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                    @endcan
                    @can('delete', $sm)
                      <form action="{{ route('instructor.sub_modules.destroy', $sm->id) }}" method="post" class="d-inline" 
                            onsubmit="return confirm('Are you sure you want to delete this sub-module?\n\nThis will also delete:\n- All contents\n- All quizzes and questions\n\nThis action cannot be undone!');">
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
                <td colspan="6" class="text-center py-4">
                  <div class="text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2">No sub-modules found. Create your first sub-module!</p>
                    @can('create', [App\Models\SubModule::class, $module])
                      <a href="{{ route('instructor.sub_modules.create', $module->id) }}" class="btn btn-primary btn-sm mt-2">
                        <i class="bi bi-plus-circle"></i> Add Sub-Module
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
    @if($subs->hasPages())
      <div class="card-footer">
        {{ $subs->links() }}
      </div>
    @endif
  </div>
</div>
@endsection

