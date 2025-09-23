@extends('layouts.instructor')

@section('title','Enrollments')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item active" aria-current="page">Enrollments</li>
  </ol>
  {{-- Binding: $enrollments (paginator), $filters, $courses --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card mb-3">
    <div class="card-body">
      <form class="form-row" method="get" action="{{ route('instructor.enrollments.index') }}">
        <div class="form-group col-md-3">
          <label class="small mb-1">Course</label>
          <select name="course" class="form-control">
            <option value="">- Semua -</option>
            @foreach($courses as $c)
              <option value="{{ $c->id }}" {{ ($filters['course'] ?? '')==$c->id ? 'selected' : '' }}>{{ $c->judul }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-2">
          <label class="small mb-1">Status</label>
          <select name="status" class="form-control">
            <option value="">- Semua -</option>
            @foreach(['active','completed','cancelled'] as $s)
              <option value="{{ $s }}" {{ ($filters['status'] ?? '')==$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-3">
          <label class="small mb-1">Dari</label>
          <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control">
        </div>
        <div class="form-group col-md-3">
          <label class="small mb-1">Sampai</label>
          <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control">
        </div>
        <div class="form-group col-md-1 d-flex align-items-end">
          <button class="btn btn-primary w-100" type="submit">Filter</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr><th>User</th><th>Course</th><th>Tanggal</th><th>Status</th><th>Selesai</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
          @forelse($enrollments as $en)
            <tr>
              <td>{{ $en->user->name ?? '-' }}</td>
              <td>{{ $en->course->judul ?? '-' }}</td>
              <td>{{ optional($en->created_at)->format('d M Y') }}</td>
              <td>{{ $en->status }}</td>
              <td>{{ optional($en->completed_at)->format('d M Y') ?: '-' }}</td>
              <td class="text-right">
                <a href="{{ route('instructor.enrollments.show', $en) }}" class="btn btn-sm btn-outline-primary">Show</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body">
      @include('partials._pagination', ['collection' => $enrollments])
    </div>
  </div>
</div>
@endsection


