@extends('layouts.instructor')

@section('title','Courses')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item active" aria-current="page">Courses</li>
  </ol>
  {{-- Binding controller: $courses (LengthAwarePaginator), $q, $filters['bidang_kompetensi'] --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card mb-3">
    <div class="card-body">
      <form class="form-inline" method="get" action="{{ route('instructor.courses.index') }}">
        <div class="form-group mr-2 mb-2">
          {{-- <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="Cari judul..."> --}}
          <label class="sr-only" for="q">{{ __('Search') }}</label>
          <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="{{ __('Search') }}">
        </div>
        <div class="form-group mr-2 mb-2">
          <select name="bidang_kompetensi" class="form-control">
            <option value="">- Bidang Kompetensi -</option>
            @foreach(($filters['options'] ?? []) as $opt)
              <option value="{{ $opt }}" {{ ($filters['bidang_kompetensi'] ?? '') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group mr-2 mb-2">
          <select name="per_page" class="form-control" onchange="this.form.submit()">
            @foreach([10,25,50] as $pp)
              <option value="{{ $pp }}" {{ (request('per_page', 10)==$pp) ? 'selected' : '' }}>{{ $pp }}/hal</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Filter</button>
        @can('create', App\Models\Course::class)
          <a href="{{ route('instructor.courses.create') }}" class="btn btn-success mb-2 ml-2">Buat Course</a>
          <a href="{{ route('instructor.courses.create-wizard') }}" class="btn btn-info mb-2 ml-2">
            <i class="bi bi-magic"></i> Buat Course (Wizard)
          </a>
        @endcan
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Judul</th>
            <th>JP</th>
            <th>Bidang Kompetensi</th>
            <th>Modules</th>
            <th>Enrollments</th>
            <th class="text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($courses as $course)
            <tr>
              <td>{{ $course->judul }}</td>
              <td>{{ $course->jp_value }}</td>
              <td>{{ $course->bidang_kompetensi }}</td>
              <td>{{ $course->modules_count ?? ($course->modules_count ?? 0) }}</td>
              <td>{{ $course->enrollments_count ?? ($course->user_enrollments_count ?? 0) }}</td>
              <td class="text-right">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('instructor.courses.show', $course) }}">Show</a>
                @can('update', $course)
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('instructor.courses.edit', $course) }}">Edit</a>
                @endcan
                @can('delete', $course)
                  <form action="{{ route('instructor.courses.destroy', $course) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus course ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                  </form>
                @endcan
                @can('duplicate', $course)
                  <form action="{{ route('instructor.courses.duplicate', $course) }}" method="post" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-info" type="submit">Duplicate</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body">
      @include('partials._pagination', ['collection' => $courses])
    </div>
  </div>
</div>
@endsection


