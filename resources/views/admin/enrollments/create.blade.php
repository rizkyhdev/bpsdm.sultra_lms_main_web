@extends('layouts.admin')

@section('title', 'Tambah Pendaftaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.enrollments.index') }}">Pendaftaran</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.enrollments.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="user_id">Pengguna</label>
                        <select id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror">
                            <option value="">-- Pilih Pengguna --</option>
                            @foreach(($usersList ?? []) as $u)
                                <option value="{{ $u->id }}" @if(old('user_id')==$u->id) selected @endif>{{ $u->nama }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="course_id">Kursus</label>
                        <select id="course_id" name="course_id" class="form-control @error('course_id') is-invalid @enderror">
                            <option value="">-- Pilih Kursus --</option>
                            @foreach(($coursesList ?? []) as $c)
                                <option value="{{ $c->id }}" @if(old('course_id')==$c->id) selected @endif>{{ $c->judul }}</option>
                            @endforeach
                        </select>
                        @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="enrollment_date">Tanggal Daftar</label>
                        <input type="date" id="enrollment_date" name="enrollment_date" value="{{ old('enrollment_date', now()->toDateString()) }}" class="form-control @error('enrollment_date') is-invalid @enderror">
                        @error('enrollment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                            @foreach(['pending'=>'Pending','active'=>'Aktif','completed'=>'Selesai','cancelled'=>'Batal'] as $val=>$label)
                                <option value="{{ $val }}" @if(old('status')===$val) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection


