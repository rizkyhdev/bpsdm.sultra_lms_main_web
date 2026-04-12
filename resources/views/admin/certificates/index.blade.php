@extends('layouts.admin')

@section('title', __('Manajemen Sertifikat'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-info text-decoration-none">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Sertifikat') }}</li>
@endsection

@section('admin_content')
    <style>
        .premium-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
            background: #fff;
        }
        .filter-section {
            background-color: #f8fafc;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
        }
        .premium-table thead th {
            background-color: #f1f5f9;
            color: #475569 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            padding: 1rem;
            border: none;
        }
        .premium-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
        }
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .course-badge {
            max-width: 250px;
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.85rem;
            color: #64748b;
        }
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .action-btn:hover {
            transform: translateY(-2px);
        }
        .form-control-premium {
            border-radius: 0.75rem;
            border-color: #e2e8f0;
            padding: 0.6rem 1rem;
        }
        .form-control-premium:focus {
            box-shadow: 0 0 0 3px rgba(33, 179, 202, 0.1);
            border-color: #21b3ca;
        }
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 mt-2">
        <div>
            <h4 class="fw-bold mb-1">{{ __('Daftar Sertifikat') }}</h4>
            <p class="text-muted small mb-0">{{ __('Kelola dan pantau penerbitan sertifikat pelatihan peserta.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-info rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal">
                <i class="fas fa-layer-group me-1"></i> {{ __('Generate Massal') }}
            </button>
            <a href="{{ route('admin.certificates.create') }}" class="btn btn-info text-white rounded-3 px-4">
                <i class="fas fa-plus-circle me-1"></i> {{ __('Terbit Manual') }}
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-section shadow-sm">
        <form action="{{ route('admin.certificates.index') }}" method="GET" class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-bold text-muted">{{ __('Cari Sertifikat') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 rounded-start-3" style="border-color: #e2e8f0;"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control form-control-premium border-start-0 ps-0" placeholder="Nomor, Nama, atau NIP..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-muted">{{ __('Filter Pelatihan') }}</label>
                <select name="course_id" class="form-select form-control-premium">
                    <option value="all">{{ __('Semua Pelatihan') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->judul }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-muted">{{ __('Rentang Tanggal') }}</label>
                <div class="d-flex gap-2">
                    <input type="date" name="date_from" class="form-control form-control-premium" value="{{ request('date_from') }}" title="Dari Tanggal">
                    <input type="date" name="date_to" class="form-control form-control-premium" value="{{ request('date_to') }}" title="Sampai Tanggal">
                </div>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-dark w-100 rounded-3 py-2">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </div>

    <div class="premium-card shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table premium-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">{{ __('Nomor Sertifikat') }}</th>
                        <th>{{ __('Penerima') }}</th>
                        <th>{{ __('Pelatihan') }}</th>
                        <th>{{ __('Tanggal Terbit') }}</th>
                        <th class="text-center">{{ __('File') }}</th>
                        <th class="text-end pe-4">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $c)
                        <tr>
                            <td class="ps-4 fw-bold text-dark" style="font-size: 0.9rem;">
                                {{ $c->nomor_sertifikat }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $c->user->avatar_url ?? 'https://www.gravatar.com/avatar/' . md5($c->user->email ?? '') . '?d=mp' }}" class="user-avatar" alt="Avatar">
                                    <div>
                                        <div class="fw-semibold small">{{ $c->user->name ?? '-' }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">NIP: {{ $c->user->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="course-badge" title="{{ $c->course->judul ?? '-' }}">
                                    {{ $c->course->judul ?? '-' }}
                                </span>
                            </td>
                            <td class="small text-secondary">
                                {{ $c->issue_date ? date('d M Y', strtotime($c->issue_date)) : '-' }}
                            </td>
                            <td class="text-center">
                                @if($c->file_path)
                                    <a href="{{ Storage::url($c->file_path) }}" target="_blank" class="action-btn bg-info bg-opacity-10 text-info" title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @else
                                    <span class="text-muted small italic">{{ __('Draft') }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.certificates.show', $c->id) }}" class="action-btn bg-light text-secondary" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.certificates.edit', $c->id) }}" class="action-btn bg-light text-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                    <button class="action-btn bg-light text-danger border-0" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmDeleteModal" 
                                            data-action="{{ route('admin.certificates.destroy', $c->id) }}"
                                            title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted opacity-50 mb-3">
                                    <i class="fas fa-certificate fa-4x"></i>
                                </div>
                                <h6 class="fw-bold">{{ __('Belum Ada Sertifikat') }}</h6>
                                <p class="text-muted small">{{ __('Gunakan tombol di atas untuk menerbitkan sertifikat.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($certificates, 'links'))
            <div class="card-footer bg-white py-3 border-0">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>

    {{-- Export Option --}}
    <div class="mt-3 text-end">
        <a href="{{ route('admin.certificates.export') }}" class="text-muted text-decoration-none small">
            <i class="fas fa-download me-1"></i> {{ __('Ekspor semua data ke Excel') }}
        </a>
    </div>

    {{-- Modal Generate Massal --}}
    <div class="modal fade" id="bulkGenerateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">{{ __('Generate Sertifikat Massal') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.certificates.bulk_generate') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-4">{{ __('Otomatis buat sertifikat untuk semua peserta yang telah dinyatakan lulus (completed) pada pelatihan terpilih.') }}</p>
                        
                        <div class="mb-3">
                            <label for="course_id_bulk" class="form-label">{{ __('Pilih Pelatihan') }}</label>
                            <select id="course_id_bulk" name="course_id" class="form-select form-control-premium" required>
                                <option value="" disabled selected>{{ __('Pilih...') }}</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->judul }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="generate_for_all" id="generateForAll" value="1">
                            <label class="form-check-label small text-muted" for="generateForAll">
                                {{ __('Timpa sertifikat yang sudah ada?') }}
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-info text-white px-4 rounded-3">{{ __('Proses Sekarang') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Delete Confirmation --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="modal-body p-4 text-center">
                    <div class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">{{ __('Hapus Sertifikat?') }}</h5>
                    <p class="text-muted small mb-4">{{ __('Tindakan ini tidak dapat dibatalkan. File sertifikat akan dihapus permanen.') }}</p>
                    
                    <form id="deleteFormCertificate" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger rounded-3">{{ __('Ya, Hapus') }}</button>
                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('confirmDeleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-action');
                const form = document.getElementById('deleteFormCertificate');
                form.setAttribute('action', action);
            });
        }
    });
</script>
@endpush
