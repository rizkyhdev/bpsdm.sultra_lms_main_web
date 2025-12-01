@extends('layouts.studentapp')

@section('title', __('Daftar Sertifikat'))

@section('content')
<div class="container-fluid my-1">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ __('Daftar Sertifikat') }}</h2>
            <p class="text-muted mb-0">{{ __('Sertifikat dari pelatihan yang telah Anda selesaikan.') }}</p>
        </div>
    </div>

    @if($enrollments->isEmpty())
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-award fs-1 text-muted mb-3"></i>
                <h5 class="fw-bold mb-1">{{ __('Belum ada sertifikat') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Selesaikan pelatihan hingga 100% untuk mendapatkan sertifikat.') }}
                </p>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Pelatihan') }}</th>
                                <th scope="col">{{ __('Bidang Kompetensi') }}</th>
                                <th scope="col">{{ __('JP') }}</th>
                                <th scope="col">{{ __('Tanggal Selesai') }}</th>
                                <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $index => $enrollment)
                                @php
                                    $course = $enrollment->course;
                                @endphp
                                <tr>
                                    <td>{{ $enrollments->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $course->judul }}</div>
                                    </td>
                                    <td>{{ $course->bidang_kompetensi ?? '-' }}</td>
                                    <td>{{ $course->jp_value ?? '-' }}</td>
                                    <td>{{ optional($enrollment->completed_at)->format('d M Y') ?? '-' }}</td>
                                    <td class="text-end">
                                        @if($course && $course->slug)
                                            <div class="btn-group" role="group" aria-label="Certificate actions">
                                                {{-- View in browser (pdf.js viewer page) --}}
                                                <a
                                                    href="{{ route('certificates.viewer', ['course' => $course->slug]) }}"
                                                    class="btn btn-sm btn-outline-primary"
                                                    target="_blank"
                                                    rel="noopener"
                                                >
                                                    <i class="bi bi-eye me-1"></i>{{ __('Lihat') }}
                                                </a>

                                                {{-- Download as file (existing behaviour via async generate) --}}
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-success js-cert-download-btn"
                                                    data-generate-url="{{ route('certificates.generate', ['course' => $course->slug]) }}"
                                                >
                                                    <i class="bi bi-download me-1"></i>{{ __('Download') }}
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted small">{{ __('Sertifikat belum tersedia') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        {{ __('Menampilkan') }}
                        {{ $enrollments->firstItem() }} - {{ $enrollments->lastItem() }}
                        {{ __('dari') }} {{ $enrollments->total() }}
                    </div>
                    <div>
                        {!! $enrollments->links() !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const downloadButtons = document.querySelectorAll('.js-cert-download-btn');
        if (!downloadButtons.length) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function showToast(message, type = 'danger') {
            const containerId = 'global-toast-container';
            let container = document.getElementById(containerId);
            if (!container) {
                container = document.createElement('div');
                container.id = containerId;
                container.style.position = 'fixed';
                container.style.top = '1rem';
                container.style.right = '1rem';
                container.style.zIndex = '1080';
                document.body.appendChild(container);
            }

            const alert = document.createElement('div');
            alert.className = 'alert alert-' + type + ' alert-dismissible fade show shadow-sm mb-2';
            alert.role = 'alert';
            alert.innerHTML = `
                <span>${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            container.appendChild(alert);

            setTimeout(function () {
                alert.classList.remove('show');
                alert.addEventListener('transitionend', function () {
                    alert.remove();
                });
            }, 4000);
        }

        function handleCertificateAction(btn, mode) {
            const originalHtml = btn.innerHTML;
            const generateUrl = btn.getAttribute('data-generate-url');

            btn.addEventListener('click', function (event) {
                event.preventDefault();
                if (!generateUrl || !csrfToken || btn.disabled) {
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __('Processing...') }}';

                fetch(generateUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                    .then(async function (response) {
                        const data = await response.json().catch(function () {
                            return null;
                        });

                        if (!response.ok || !data) {
                            throw new Error(data && data.message ? data.message : 'Failed to generate certificate.');
                        }

                        if (!data.success || !data.download_url) {
                            throw new Error(data.message || 'Failed to generate certificate.');
                        }

                        // Ensure we can append query params safely
                        const separator = data.download_url.includes('?') ? '&' : '?';

                        if (mode === 'view') {
                            const viewUrl = data.download_url + separator + 'mode=view';
                            window.open(viewUrl, '_blank', 'noopener,noreferrer');
                            showToast('{{ __('Certificate opened in a new tab.') }}', 'success');
                        } else {
                            window.location.href = data.download_url;
                            showToast('{{ __('Certificate download started.') }}', 'success');
                        }
                    })
                    .catch(function (error) {
                        console.error(error);
                        showToast(error.message || 'Failed to generate certificate.', 'danger');
                    })
                    .finally(function () {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
            });
        }

        downloadButtons.forEach(function (btn) {
            handleCertificateAction(btn, 'download');
        });
    });
</script>
@endsection


