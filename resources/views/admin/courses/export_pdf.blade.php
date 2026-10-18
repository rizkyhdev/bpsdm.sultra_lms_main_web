<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Kursus</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #334155;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            color: #1e3a8a;
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }
        .meta-info {
            margin-bottom: 20px;
            font-size: 12px;
            color: #475569;
            width: 100%;
        }
        .meta-info td {
            border: none;
            padding: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BPSDM LMS Sobat ASR</h1>
        <p>Laporan Rekapitulasi Data Kursus</p>
    </div>
    
    <table class="meta-info" style="margin-bottom: 20px;">
        <tr>
            <td width="120"><strong>Tanggal Cetak</strong></td>
            <td>: {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ auth()->user()->name ?? 'Administrator' }}</td>
        </tr>
        <tr>
            <td><strong>Total Kursus</strong></td>
            <td>: {{ $courses->count() }} kursus</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Judul Kursus</th>
                <th width="20%">Bidang Kompetensi</th>
                <th width="10%" class="text-center">Total JP</th>
                <th width="15%" class="text-center">Jumlah Modul</th>
                <th width="15%" class="text-center">Total Pendaftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($courses as $index => $course)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $course->judul }}</strong></td>
                <td>{{ $course->bidang_kompetensi }}</td>
                <td class="text-center">{{ $course->jp_value }} JP</td>
                <td class="text-center">{{ $course->modules_count ?? 0 }}</td>
                <td class="text-center">{{ $course->user_enrollments_count ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data kursus yang ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem LMS BPSDM. Dokumen ini sah dan tidak memerlukan tanda tangan basah.
    </div>
</body>
</html>
