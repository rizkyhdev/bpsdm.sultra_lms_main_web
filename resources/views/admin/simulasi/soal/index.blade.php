@extends('layouts.admin')

@section('title', 'Bank Soal')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.simulasi.index') }}">Simulasi Uji Kompetensi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Bank Soal</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.simulasi.soal.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Tambah Soal
    </a>
@endsection

@section('admin_content')
    @include('admin.simulasi._nav')

    {{-- Pencarian --}}
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.simulasi.soal.index') }}" class="row g-2 align-items-center">
                <div class="col-md">
                    <label for="search" class="visually-hidden">Cari soal</label>
                    <input type="search" id="search" name="search" value="{{ $search }}" class="form-control"
                           placeholder="Cari pertanyaan atau pilihan jawaban…">
                </div>
                <div class="col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1" aria-hidden="true"></i>Cari</button>
                    @if ($search !== '')
                        <a href="{{ route('admin.simulasi.soal.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
                <div class="col-md-auto text-muted small">
                    Total <strong class="text-body">{{ number_format($totalSoal, 0, ',', '.') }}</strong> soal
                    @if ($search !== '')
                        &middot; {{ $questions->total() }} hasil
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar soal --}}
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 4rem;">No</th>
                        <th scope="col">Soal &amp; Pilihan Jawaban</th>
                        <th scope="col" class="text-center">Kunci</th>
                        <th scope="col" class="text-center">Dijawab</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($questions as $question)
                        <tr>
                            <td class="text-center text-muted">{{ $questions->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="fw-semibold mb-1">{{ Str::limit($question->pertanyaan, 220) }}</div>
                                <ol type="A" class="small mb-0 ps-3">
                                    @foreach ($question->options() as $huruf => $teks)
                                        <li @class(['text-success fw-semibold' => $huruf === $question->kunci_jawaban, 'text-muted' => $huruf !== $question->kunci_jawaban])>
                                            {{ Str::limit($teks, 120) }}
                                        </li>
                                    @endforeach
                                </ol>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">{{ $question->kunci_jawaban }}</span>
                            </td>
                            <td class="text-center">{{ $question->answers_count }}&times;</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.simulasi.soal.edit', $question) }}" class="btn btn-sm btn-outline-primary" title="Edit soal">
                                        <i class="fas fa-edit" aria-hidden="true"></i><span class="visually-hidden">Edit</span>
                                    </a>
                                    <form action="{{ route('admin.simulasi.soal.destroy', $question) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                @disabled($question->answers_count > 0)
                                                title="{{ $question->answers_count > 0 ? 'Soal sudah dijawab peserta sehingga tidak dapat dihapus' : 'Hapus soal' }}">
                                            <i class="fas fa-trash" aria-hidden="true"></i><span class="visually-hidden">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                {{ $search !== '' ? 'Tidak ada soal yang cocok dengan pencarian.' : 'Belum ada soal di bank soal.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($questions->hasPages())
            <div class="card-body">{{ $questions->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
@endsection
