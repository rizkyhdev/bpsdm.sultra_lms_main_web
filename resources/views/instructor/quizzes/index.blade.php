@extends('layouts.instructor')

@section('title','Quizzes')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.sub_modules.show', $subModule) }}">{{ $subModule->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Quizzes</li>
  </ol>
  {{-- Binding: $subModule, $quizzes --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-end mb-3">
    @can('create', [App\Models\Quiz::class, $subModule])
      <a href="{{ route('instructor.sub_modules.quizzes.create', $subModule) }}" class="btn btn-primary btn-sm">Tambah Quiz</a>
    @endcan
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr><th>Judul</th><th>Nilai Minimum</th><th>Maks Attempt</th><th>Questions</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
          @forelse($quizzes as $q)
            <tr>
              <td>{{ $q->judul }}</td>
              <td>{{ $q->nilai_minimum }}</td>
              <td>{{ $q->max_attempts }}</td>
              <td>{{ $q->questions_count ?? 0 }}</td>
              <td class="text-right">
                <a href="{{ route('instructor.quizzes.show', $q) }}" class="btn btn-sm btn-outline-primary">Show</a>
                @can('update', $q)
                  <a href="{{ route('instructor.quizzes.edit', $q) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
                @can('delete', $q)
                  <form action="{{ route('instructor.quizzes.destroy', $q) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus quiz ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                @endcan
                <a href="{{ route('instructor.quizzes.results', $q) }}" class="btn btn-sm btn-outline-info">Results</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center">Belum ada quiz</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection


