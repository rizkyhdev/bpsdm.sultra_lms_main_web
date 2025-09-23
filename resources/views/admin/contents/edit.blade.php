@extends('layouts.admin')

@section('title', 'Ubah Konten')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sub_modules.show', $content->sub_module_id) }}">Sub Modul</a></li>
    <li class="breadcrumb-item active">Ubah Konten</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.contents.update', $content) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="judul">Judul</label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul', $content->judul) }}" class="form-control @error('judul') is-invalid @enderror">
                        @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tipe">Tipe</label>
                        <select id="tipe" name="tipe" class="form-control @error('tipe') is-invalid @enderror">
                            @foreach(['text','pdf','video','audio','image'] as $t)
                                <option value="{{ $t }}" @if(old('tipe', $content->tipe)===$t) selected @endif>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                        @error('tipe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label for="urutan">Urutan</label>
                        <input type="number" id="urutan" name="urutan" value="{{ old('urutan', $content->urutan) }}" class="form-control @error('urutan') is-invalid @enderror">
                        @error('urutan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>File Saat Ini</label>
                    <div>
                        @if($content->file_path)
                            <a href="{{ Storage::url($content->file_path) }}" target="_blank">Lihat/Unduh</a>
                        @else
                            <span class="text-muted">Tidak ada</span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label for="file">Ganti File (opsional)</label>
                    <input type="file" id="file" name="file" class="form-control-file @error('file') is-invalid @enderror">
                    @error('file')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.sub_modules.show', $content->sub_module_id) }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection


