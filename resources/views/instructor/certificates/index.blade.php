@extends('layouts.instructor')

@section('title', __('Sertifikat Peserta'))

@section('content')
<div class="container-fluid my-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold mb-1">{{ __('Sertifikat Peserta') }}</h2>
            <p class="text-muted mb-0">
                {{ __('Daftar sertifikat yang telah diterbitkan untuk peserta pada pelatihan yang Anda kelola.') }}
            </p>
        </div>
    </div>

    <form method="GET" action="{{ route('instructor.certificates.index') }}" class="mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-6 mb-2">
                        <label for="search" class="form-label">{{ __('Cari Sertifikat / Peserta / Pelatihan') }}</label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="{{ __('Nomor sertifikat, nama peserta, email, atau judul pelatihan') }}"
                        >
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="per_page" class="form-label">{{ __('Per Halaman') }}</label>
                        <select id="per_page" name="per_page" class="form-control">
                            @foreach([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @selected(request('per_page', 15) == $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-search me-1"></i>{{ __('Cari') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">{{ __('Nomor Sertifikat') }}</th>
                        <th scope="col">{{ __('Peserta') }}</th>
                        <th scope="col">{{ __('Pelatihan') }}</th>
                        <th scope="col">{{ __('Tanggal Terbit') }}</th>
                        <th scope="col">{{ __('File') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $certificate)
                        <tr>
                            <td class="fw-semibold">
                                {{ $certificate->nomor_sertifikat ?? '-' }}
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    {{ $certificate->user->name ?? '-' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $certificate->user->email ?? '' }}
                                </div>
                            </td>
                            <td>
                                {{ $certificate->course->judul ?? '-' }}
                            </td>
                            <td>
                                {{ optional($certificate->issue_date)->format('d M Y') ?? '-' }}
                            </td>
                            <td>
                                @if($certificate->file_path)
                                    <a
                                        href="{{ Storage::url($certificate->file_path) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="fas fa-download me-1"></i>{{ __('Unduh') }}
                                    </a>
                                @else
                                    <span class="text-muted small">{{ __('Belum ada file') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                {{ __('Belum ada sertifikat untuk pelatihan yang Anda kelola.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($certificates->hasPages())
            <div class="card-body">
                {!! $certificates->links() !!}
            </div>
        @endif
    </div>
</div>
@endsection

