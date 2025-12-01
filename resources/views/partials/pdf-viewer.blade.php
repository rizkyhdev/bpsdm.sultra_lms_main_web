@php
    /**
     * Reusable PDF viewer using pdf.js
     *
     * Expected variables:
     * - string $pdfUrl        : absolute URL to the PDF file (same-origin)
     * - string|null $downloadUrl : optional explicit download URL; falls back to $pdfUrl
     * - string|null $title    : optional document title shown in toolbar
     */
    $viewerId = $viewerId ?? ('pdfViewer_' . uniqid());
    $downloadUrl = $downloadUrl ?? $pdfUrl;
@endphp

<div id="{{ $viewerId }}" class="pdfjs-viewer-container mb-3" data-pdf-url="{{ $pdfUrl }}" data-download-url="{{ $downloadUrl }}">
    <div class="pdfjs-toolbar d-flex align-items-center justify-content-between mb-2 px-2 py-1 bg-light border rounded-top">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary pdfjs-btn-prev" title="{{ __('Halaman Sebelumnya') }}">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary pdfjs-btn-next" title="{{ __('Halaman Berikutnya') }}">
                <i class="bi bi-chevron-right"></i>
            </button>
            <span class="small text-muted ms-1">
                <span class="pdfjs-page-num">1</span>
                / <span class="pdfjs-page-count">1</span>
            </span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary pdfjs-btn-zoom-out" title="{{ __('Perkecil') }}">
                <i class="bi bi-zoom-out"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary pdfjs-btn-zoom-in" title="{{ __('Perbesar') }}">
                <i class="bi bi-zoom-in"></i>
            </button>
            <span class="small text-muted ms-1">
                <span class="pdfjs-zoom-percent">100</span>%
            </span>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(!empty($title))
                <span class="small text-truncate" style="max-width: 220px;" title="{{ $title }}">
                    <i class="bi bi-file-earmark-pdf me-1 text-danger"></i>{{ $title }}
                </span>
            @endif
            @if($downloadUrl)
                <a href="{{ $downloadUrl }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                    <i class="bi bi-download me-1"></i>{{ __('Unduh') }}
                </a>
            @endif
        </div>
    </div>

    <div class="pdfjs-canvas-wrapper border border-top-0 rounded-bottom bg-white d-flex justify-content-center align-items-start" style="min-height: 400px; max-height: 80vh; overflow: auto;">
        <canvas class="pdfjs-canvas" style="max-width: 100%; height: auto;"></canvas>
    </div>

    {{-- Fallback iframe if pdf.js fails --}}
    <div class="pdfjs-fallback-wrapper border border-top-0 rounded-bottom bg-white d-none" style="min-height: 400px; max-height: 80vh;">
        <iframe class="pdfjs-fallback-iframe w-100 h-100" src="{{ $pdfUrl }}" style="border: none;"></iframe>
    </div>

    <div class="pdfjs-loading text-center py-3 small text-muted d-none">
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        {{ __('Memuat dokumen PDF...') }}
    </div>

    <div class="pdfjs-error alert alert-danger mt-2 d-none small" role="alert">
        {{ __('Gagal memuat dokumen PDF. Menampilkan tampilan standar browser.') }}
    </div>
</div>

@once
    {{-- pdf.js runtime is bundled via Vite in resources/js/pdf-viewer.js and loaded from layouts that include student.js --}}
@endonce


