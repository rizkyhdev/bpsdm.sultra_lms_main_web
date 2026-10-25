@extends('layouts.studentapp')

@section('title', 'Simulasi Ujian')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dasbor</a></li>
            <li class="breadcrumb-item active" aria-current="page">Simulasi Ujian</li>
        </ol>
    </nav>

    @include('partials._flash')

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            {{-- Informasi simulasi --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body">
                    <h1 class="h3 fw-bold mb-2">Simulasi Uji Kompetensi (CAT)</h1>
                    <p class="text-muted mb-3">
                        Latih kesiapan Anda menghadapi uji kompetensi berbasis komputer (<em>Computer Assisted Test</em>)
                        dengan soal-soal dari bank soal BPSDM Provinsi Sulawesi Tenggara.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if ($jumlahSoal)
                            <span class="badge bg-primary"><i class="bi bi-question-circle me-1"></i>{{ $jumlahSoal }} Soal Acak</span>
                            <span class="badge bg-info"><i class="bi bi-clock me-1"></i>{{ $durasiMenit }} Menit</span>
                        @endif
                        <span class="badge bg-secondary"><i class="bi bi-ui-radios me-1"></i>Pilihan Ganda A&ndash;D</span>
                    </div>
                    <h2 class="h6 fw-semibold">Ketentuan</h2>
                    <ul class="small text-muted mb-0">
                        <li>Soal diambil secara acak dari bank soal untuk setiap peserta.</li>
                        <li>Waktu berjalan sejak simulasi dimulai dan tetap berjalan meskipun halaman ditutup.</li>
                        <li>Jawaban tersimpan otomatis setiap kali Anda memilih jawaban, dan Anda dapat berpindah soal kapan saja.</li>
                        <li>Simulasi berakhir ketika Anda menekan <strong>Selesai</strong> atau ketika waktu habis.</li>
                        <li>Skor dihitung dari jumlah jawaban benar (1 soal benar = 1 poin).</li>
                    </ul>
                </div>
            </div>

            {{-- Mulai / lanjutkan --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body">
                    @if ($ongoing)
                        <div class="alert alert-warning">
                            <i class="bi bi-hourglass-split me-2"></i>
                            <strong>Simulasi sedang berlangsung.</strong>
                            Sisa waktu sekitar {{ (int) ceil($ongoing->remainingSeconds() / 60) }} menit.
                        </div>
                        <a href="{{ route('student.simulasi.exam', $ongoing) }}" class="btn btn-primary w-100">
                            <i class="bi bi-play-circle me-1"></i>Lanjutkan Simulasi
                        </a>
                    @elseif ($bankSoal === 0)
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>Bank soal belum tersedia. Silakan hubungi admin.
                        </div>
                    @else
                        <h2 class="h5 fw-bold mb-1">Mulai Simulasi</h2>
                        <p class="text-muted small">Masukkan token ujian yang diberikan oleh panitia atau admin.</p>
                        <form action="{{ route('student.simulasi.start') }}" method="POST" class="row g-2">
                            @csrf
                            <div class="col-sm">
                                <label for="token" class="visually-hidden">Token ujian</label>
                                <input type="text" id="token" name="token" value="{{ old('token') }}" maxlength="10" required
                                       autocomplete="off" autocapitalize="characters" spellcheck="false"
                                       class="form-control form-control-lg text-uppercase font-monospace @error('token') is-invalid @enderror"
                                       placeholder="Token ujian">
                                @error('token')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-auto">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-play-circle me-1"></i>Mulai Simulasi
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Riwayat --}}
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h2 class="h5 fw-bold mb-0">Riwayat Simulasi</h2>
                </div>
                <div class="card-body">
                    @if ($history->isEmpty())
                        <p class="text-muted mb-0">Belum ada simulasi yang diselesaikan.</p>
                    @else
                        <div class="list-group">
                            @foreach ($history as $attempt)
                                <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $attempt->token?->sesi }}</div>
                                        <small class="text-muted">{{ $attempt->completed_at->format('d M Y H:i') }}</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="fw-bold">
                                            {{ $attempt->skor }} <span class="text-muted fw-normal small">/ {{ $attempt->jumlah_soal }}</span>
                                        </span>
                                        <a href="{{ route('student.simulasi.result', $attempt) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>Lihat Hasil
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($history->hasPages())
                            <div class="mt-3">{{ $history->links('pagination::bootstrap-5') }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Data peserta --}}
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h2 class="h5 fw-bold mb-0">Data Peserta</h2>
                </div>
                <div class="card-body">
                    <dl class="row g-2 mb-0 small">
                        <dt class="col-sm-4 text-muted">Nama</dt>
                        <dd class="col-sm-8">{{ $user->name }}</dd>
                        <dt class="col-sm-4 text-muted">NIP</dt>
                        <dd class="col-sm-8">{{ $user->nip ?: '-' }}</dd>
                        <dt class="col-sm-4 text-muted">Unit Kerja</dt>
                        <dd class="col-sm-8">{{ $user->unit_kerja ?: '-' }}</dd>
                    </dl>
                    <p class="small text-muted mt-3 mb-0">
                        Data peserta diambil dari profil Anda.
                        <a href="{{ route('student.profile.edit') }}">Perbarui profil</a> bila ada yang keliru.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
