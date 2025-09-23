@extends('layouts.admin')

@section('title', 'Ubah Pendaftaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.enrollments.index') }}">Pendaftaran</a></li>
    <li class="breadcrumb-item active">Ubah</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.enrollments.update', $enrollment) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="enrollment_date">Tanggal Daftar</label>
                        <input type="date" id="enrollment_date" name="enrollment_date" value="{{ old('enrollment_date', optional($enrollment->enrollment_date)->toDateString()) }}" class="form-control @error('enrollment_date') is-invalid @enderror">
                        @error('enrollment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                            @foreach(['pending'=>'Pending','active'=>'Aktif','completed'=>'Selesai','cancelled'=>'Batal'] as $val=>$label)
                                <option value="{{ $val }}" @if(old('status', $enrollment->status)===$val) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="completed_at">Tanggal Selesai</label>
                        <input type="datetime-local" id="completed_at" name="completed_at" value="{{ old('completed_at', optional($enrollment->completed_at)->format('Y-m-d\TH:i')) }}" class="form-control @error('completed_at') is-invalid @enderror">
                        @error('completed_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection


