@php
    /**
     * Simple Google Drive PDF viewer
     *
     * Expected variables:
     * - string $driveUrl : original Google Drive sharing URL
     * - string|null $title : optional document title
     */

    $title = $title ?? null;

    $embedUrl = $driveUrl;

    // Try to normalise common Google Drive URL formats to the /preview endpoint
    if (\Illuminate\Support\Str::contains($driveUrl, 'drive.google.com')) {
        // Pattern: https://drive.google.com/file/d/{FILE_ID}/view?usp=sharing
        if (preg_match('~/d/([^/]+)/~', $driveUrl, $matches)) {
            $fileId = $matches[1] ?? null;
        } else {
            // Pattern: https://drive.google.com/open?id={FILE_ID} or ?id={FILE_ID}
            $queryString = parse_url($driveUrl, PHP_URL_QUERY);
            $params = [];
            if ($queryString) {
                parse_str($queryString, $params);
            }
            $fileId = $params['id'] ?? null;
        }

        if (!empty($fileId)) {
            $embedUrl = 'https://drive.google.com/file/d/' . $fileId . '/preview';
        }
    }
@endphp

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF (Google Drive)
            </span>
            @if($title)
                <span class="small text-truncate" style="max-width: 260px;" title="{{ $title }}">
                    {{ $title }}
                </span>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ $driveUrl }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('Buka di Tab Baru') }}
            </a>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="ratio ratio-16x9" style="min-height: 400px;">
            <iframe
                src="{{ $embedUrl }}"
                style="border: 0;"
                allow="autoplay"
                loading="lazy"
            ></iframe>
        </div>
        <p class="text-muted small mt-2 mb-0">
            {{ __('Dokumen ini dimuat langsung dari Google Drive. Jika tampilan tidak muncul, klik tombol "Buka di Tab Baru".') }}
        </p>
    </div>
</div>


