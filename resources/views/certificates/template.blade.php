<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Certificate') }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #001f54;
            background-color: #ffffff;
        }

        .page {
            position: relative;
            width: 100%;
            height: 100%;
            page-break-after: always;
            overflow: hidden;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .page-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.08;
            z-index: -1;
        }

        .page-content {
            position: absolute;
            top: 15mm;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
            border: 2px solid #003f7d;
            padding: 24px 32px;
            z-index: 1;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            height: 40px;
        }

        .title-wrapper {
            margin-top: 32px;
            text-align: center;
        }

        .title-main {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            border-bottom: 3px solid #003f7d;
            display: inline-block;
            padding-bottom: 6px;
        }

        .subtitle {
            margin-top: 28px;
            font-size: 14px;
        }

        .student-name {
            margin-top: 18px;
            font-size: 28px;
            font-weight: 700;
            color: #003f7d;
        }

        .description-text {
            margin-top: 18px;
            font-size: 14px;
        }

        .course-title {
            margin-top: 12px;
            font-size: 18px;
            font-weight: 700;
            color: #003f7d;
        }

        .course-meta {
            margin-top: 32px;
            font-size: 13px;
            text-align: center;
        }

        .signature-section {
            position: absolute;
            bottom: 180px; /* Increased from 40px */
            left: 0;
            right: 0;
            text-align: center;
        }

        .signature-block {
            margin: 0 auto;
            width: 260px;
        }

        .signature-line {
            margin-top: 40px; /* Slightly reduced */
            border-top: 1px solid #000;
            width: 100%;
        }

        .signature-name {
            margin-top: 8px;
            font-size: 14px;
            font-weight: 700;
        }

        .signature-title {
            font-size: 11px;
            margin-top: 4px;
        }

        .footer-row {
            position: absolute;
            bottom: 30px; /* Increased from 10px */
            left: 40px;
            right: 40px;
            font-size: 10px;
        }

        .uid-text {
            font-family: 'Courier New', monospace;
        }

        /* Second page: competencies table */
        .second-title-row {
            display: block;
            margin-bottom: 20px;
        }

        .second-title {
            font-size: 18px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th, td {
            padding: 6px 8px;
            border: 0.5px solid #003f7d;
        }

        th {
            background-color: #003f7d;
            color: #ffffff;
            text-align: left;
        }

        td:first-child, th:first-child {
            width: 40px;
            text-align: center;
        }

        .score-box {
            position: absolute;
            bottom: 120px;
            right: 40px;
            border: 1.5px solid #003f7d;
            width: 180px;
        }

        .score-box-header {
            background-color: #003f7d;
            color: #ffffff;
            font-size: 11px;
            text-align: center;
            padding: 6px 4px;
        }

        .score-box-body {
            text-align: center;
            padding: 12px 4px;
            font-size: 26px;
            font-weight: 800;
        }
    </style>
</head>
@php
    $jp = $jp_value ?? null;
    $score = $final_score ?? null;
    $competencyList = isset($competencies) && is_array($competencies) ? $competencies : [];
    
    // Convert images to base64 to ensure they show up in PDF
    $logoSultraPath = public_path('image/logo_sultra_images.png');
    $logoBpsdmPath = public_path('image/logo bpsdm.jpeg');
    
    $logoSultraBase64 = '';
    if (file_exists($logoSultraPath)) {
        $logoSultraBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoSultraPath));
    }
    
    $logoBpsdmBase64 = '';
    if (file_exists($logoBpsdmPath)) {
        $logoBpsdmBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoBpsdmPath));
    }
@endphp
<body>
    {{-- PAGE 1: main certificate --}}
    <div class="page">
        @if($background_image)
            <img src="{{ $background_image }}" alt="" class="page-background">
        @endif

        <div class="page-content">
            <div class="header-row clearfix" style="display: block; width: 100%;">
                <div style="float: left;">
                    @if($logoSultraBase64)
                        <img src="{{ $logoSultraBase64 }}" alt="Logo Sultra" height="60">
                    @else
                        <span>LOGO SULTRA</span>
                    @endif
                </div>
                <div style="float: right;">
                    @if($logoBpsdmBase64)
                        <img src="{{ $logoBpsdmBase64 }}" alt="Logo BPSDM" height="60">
                    @else
                        <span>LOGO BPSDM</span>
                    @endif
                </div>
                <div style="clear: both;"></div>
            </div>

            <div class="title-wrapper">
                <div class="title-main">SERTIFIKAT</div>
                <div class="subtitle">
                    dengan bangga mempersembahkan sertifikat ini kepada:
                </div>
                <div class="student-name">
                    {{ $student_name }}
                </div>
                <div class="description-text">
                    Atas pencapaiannya dalam menyelesaikan tugas akhir pada kelas pelatihan:
                </div>
                <div class="course-title">
                    {{ $course_title }}@if($jp) ({{ $jp }} JP) @endif
                </div>
            </div>

            <div class="course-meta">
                Kendari, {{ $completion_date }}
            </div>

            <div class="signature-section">
                <div class="signature-block">
                    {{-- Space for handwritten signature image can be added here if desired --}}
                    <div class="signature-line"></div>
                    <div class="signature-name">
                        {{ $issuer_name }}
                    </div>
                    <div class="signature-title">
                        Deputi Bidang Transformasi Pembelajaran ASN
                    </div>
                </div>
            </div>

            <div class="footer-row">
                <div class="uid-text">
                    ID Sertifikat: {{ $certificate_uid }}
                </div>
                <div>
                    {{-- QR code placeholder, if you later add one via DomPDF or image --}}
                </div>
            </div>
        </div>
    </div>

    {{-- PAGE 2: competencies + score --}}
    <div class="page">
        @if($background_image)
            <img src="{{ $background_image }}" alt="" class="page-background">
        @endif

        <div class="page-content">
            <div class="second-title-row">
                <div class="second-title">
                    {{ $course_title }}@if($jp) ({{ $jp }} JP) @endif
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kompetensi yang dipelajari</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($competencyList as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td>1</td>
                            <td>{{ $course_title }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- <div class="score-box">
                <div class="score-box-header">
                    Sertifikat ini Bernilai
                </div>
                <div class="score-box-body">
                    {{ $score !== null ? $score : '—' }}
                </div>
            </div> -->
        </div>
    </div>
</body>
</html>

