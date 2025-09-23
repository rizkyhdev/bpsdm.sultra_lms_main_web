@extends('layouts.instructor')

@section('title','Questions')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.show', $quiz) }}">{{ $quiz->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Questions</li>
  </ol>
  {{-- Binding: $quiz, $questions --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    @can('create', [App\Models\Question::class, $quiz])
      <a href="{{ route('instructor.questions.create', $quiz->id) }}" class="btn btn-primary btn-sm">Tambah Pertanyaan</a>
    @endcan
  </div>

  <div class="card mb-3">
    <div class="card-header">Reorder</div>
    <div class="card-body">
      <form action="{{ route('instructor.questions.reorder') }}" method="post">
        @csrf
        <div class="form-group">
          <label>JSON Payload</label>
          <textarea name="items" class="form-control" rows="4" placeholder='{"items":[{"id":1,"urutan":1}]}'></textarea>
        </div>
        <button type="submit" class="btn btn-outline-secondary">Simpan Urutan</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr><th>Urutan</th><th>Pertanyaan</th><th>Tipe</th><th>Bobot</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
          @forelse($questions as $q)
            <tr>
              <td>{{ $q->urutan }}</td>
              <td class="text-truncate" style="max-width: 420px;">{{ $q->pertanyaan }}</td>
              <td>{{ $q->tipe }}</td>
              <td>{{ $q->bobot }}</td>
              <td class="text-right">
                <a href="{{ route('instructor.questions.show', $q) }}" class="btn btn-sm btn-outline-primary">Show</a>
                @can('update', $q)
                  <a href="{{ route('instructor.questions.edit', $q) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
                @can('delete', $q)
                  <form action="{{ route('instructor.questions.destroy', $q) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus pertanyaan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center">Belum ada pertanyaan</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection


