@extends('layouts.instructor')

@section('title','All Quizzes')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item active" aria-current="page">All Quizzes</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">All Quizzes</h5>
      <small>Daftar semua quiz dari semua course Anda</small>
    </div>
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Judul</th>
              <th>Level</th>
              <th>Course/Module/Sub-Module</th>
              <th>Nilai Minimum</th>
              <th>Maks Attempt</th>
              <th>Questions</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($quizzes as $q)
              <tr>
                <td>{{ $q->judul }}</td>
                <td>
                  @if($q->sub_module_id)
                    <span class="badge bg-info">Sub-Module</span>
                  @elseif($q->module_id)
                    <span class="badge bg-warning">Module</span>
                  @elseif($q->course_id)
                    <span class="badge bg-success">Course</span>
                  @endif
                </td>
                <td>
                  @if($q->sub_module_id)
                    <small>{{ $q->subModule->judul ?? 'N/A' }}</small><br>
                    <small class="text-muted">Module: {{ $q->subModule->module->judul ?? 'N/A' }}</small><br>
                    <small class="text-muted">Course: {{ $q->subModule->module->course->judul ?? 'N/A' }}</small>
                  @elseif($q->module_id)
                    <small>{{ $q->module->judul ?? 'N/A' }}</small><br>
                    <small class="text-muted">Course: {{ $q->module->course->judul ?? 'N/A' }}</small>
                  @elseif($q->course_id)
                    <small>{{ $q->course->judul ?? 'N/A' }}</small>
                  @endif
                </td>
                <td>{{ $q->nilai_minimum }}</td>
                <td>{{ $q->max_attempts }}</td>
                <td>{{ $q->questions_count ?? 0 }}</td>
                <td class="text-right">
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('instructor.quizzes.show', $q) }}" class="btn btn-outline-primary">Show</a>
                    @can('update', $q)
                      <a href="{{ route('instructor.quizzes.edit', $q) }}" class="btn btn-outline-secondary">Edit</a>
                    @endcan
                    @can('delete', $q)
                      <form action="{{ route('instructor.quizzes.destroy', $q) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus quiz ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">Delete</button>
                      </form>
                    @endcan
                    <a href="{{ route('instructor.quizzes.results', $q) }}" class="btn btn-outline-info">Results</a>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center">Belum ada quiz</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      @if($quizzes->hasPages())
        <div class="mt-3">
          {{ $quizzes->links() }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

