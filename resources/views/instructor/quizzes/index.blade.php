@extends('layouts.instructor')

@section('title','Quizzes')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.sub_modules.show', $sub) }}">{{ $sub->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Quizzes</li>
  </ol>
  {{-- Binding: $sub, $quizzes --}}
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
      <h4 class="mb-0">Quizzes</h4>
      <small class="text-muted">Sub-Module: {{ $sub->judul }}</small>
    </div>
    <div>
      <a href="{{ route('instructor.sub_modules.show', $sub->id) }}" class="btn btn-light btn-sm me-2">
        <i class="bi bi-arrow-left"></i> Back to Sub-Module
      </a>
      @can('create', [App\Models\Quiz::class, $sub])
        <a href="{{ route('instructor.quizzes.create', $sub->id) }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-circle"></i> Add Quiz
        </a>
      @endcan
    </div>
  </div>
  
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Quizzes List ({{ $quizzes->total() }})</span>
    </div>
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead class="table-light">
          <tr>
            <th>Title</th>
            <th width="120" class="text-center">Min Score</th>
            <th width="120" class="text-center">Max Attempts</th>
            <th width="120" class="text-center">Questions</th>
            <th width="250" class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($quizzes as $q)
            <tr>
              <td>
                <strong>{{ $q->judul }}</strong>
                @if($q->deskripsi)
                  <br><small class="text-muted">{{ Str::limit($q->deskripsi, 80) }}</small>
                @endif
              </td>
              <td class="text-center">
                <span class="badge bg-warning">{{ $q->nilai_minimum }}%</span>
              </td>
              <td class="text-center">
                <span class="badge bg-info">{{ $q->max_attempts }}</span>
              </td>
              <td class="text-center">
                <span class="badge bg-secondary">{{ $q->questions_count ?? 0 }}</span>
              </td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="{{ route('instructor.quizzes.show', $q) }}" class="btn btn-outline-primary" title="View">
                    <i class="bi bi-eye"></i>
                  </a>
                  @can('update', $q)
                    <a href="{{ route('instructor.quizzes.edit', $q) }}" class="btn btn-outline-secondary" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                  @endcan
                  @can('delete', $q)
                    <form action="{{ route('instructor.quizzes.destroy', $q) }}" method="post" class="d-inline" 
                          onsubmit="return confirm('Are you sure you want to delete this quiz?\n\nThis will also delete:\n- All questions\n- All answer options\n\nThis action cannot be undone!');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  @endcan
                  <a href="{{ route('instructor.quizzes.results', $q) }}" class="btn btn-outline-info" title="Results">
                    <i class="bi bi-bar-chart"></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4">
                <div class="text-muted">
                  <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                  <p class="mt-2">No quizzes found. Create your first quiz!</p>
                  @can('create', [App\Models\Quiz::class, $sub])
                    <a href="{{ route('instructor.quizzes.create', $sub->id) }}" class="btn btn-primary btn-sm mt-2">
                      <i class="bi bi-plus-circle"></i> Add Quiz
                    </a>
                  @endcan
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($quizzes->hasPages())
      <div class="card-footer">
        {{ $quizzes->links() }}
      </div>
    @endif
  </div>
</div>
@endsection


