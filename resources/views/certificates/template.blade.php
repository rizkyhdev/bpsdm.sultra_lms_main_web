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
            /* Explicit A4 landscape dimensions so browser print preview respects orientation */
            width: 297mm;
            height: 210mm;
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
            width: 90%;
            height: 90%;
            /* Remove internal padding so the border can be centered consistently
               and use an inner margin on .page-content instead. This helps DomPDF
               produce a certificate that is properly aligned when printed. */
        }

        .page-background {
            position: absolute;
            inset: 0;
            opacity: 0.08;
            z-index: 0;
        }

        .page-content {
            position: relative;
            z-index: 1;
            /* Add uniform margins from the paper edge so the border is visually centered */
            margin: 24px 32px;
            width: calc(100% - 64px);
            height: calc(100% - 48px);
            border: 2px solid #003f7d;
            padding: 28px 40px;
            display: flex;
            flex-direction: column;
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
            margin-top: auto;
            display: flex;
            justify-content: center;
            text-align: center;
        }

        .signature-block {
            min-width: 260px;
        }

        .signature-line {
            margin-top: 56px;
            border-top: 1px solid #000;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
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
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 10px;
        }

        .uid-text {
            font-family: 'Courier New', monospace;
        }

        .page-break {
            page-break-before: always;
        }

        /* Second page: competencies table */
        .second-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            margin-top: 24px;
            margin-left: auto;
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
@endphp
<body>
    {{-- PAGE 1: main certificate --}}
    <div class="page">
        @if($background_image)
            <img src="{{ $background_image }}" alt="" class="page-background">
        @endif

        <div class="page-content">
            <div class="header-row">
                {{-- Left logo placeholder (configure in background or edit template as needed) --}}
                <div>
                    {{-- You can replace this with an <img> pointing to the LAN RI logo --}}
                    <img src="{{ asset('image/LOGO AURA.png') }}" alt="Logo" height="40">
                </div>
                <div>
                    {{-- You can replace this with an <img> pointing to the ASN BERPIJAR logo --}}
                    <img src="{{ asset('image/LOGO AURA 1.png') }}" alt="Logo" height="40">
                </div>
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
                Jakarta, {{ $completion_date }}
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
    <div class="page page-break">
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

            <div class="score-box">
                <div class="score-box-header">
                    Sertifikat ini Bernilai
                </div>
                <div class="score-box-body">
                    {{ $score !== null ? $score : '—' }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>

