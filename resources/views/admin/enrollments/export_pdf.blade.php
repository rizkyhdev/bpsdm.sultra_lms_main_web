<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Pendaftaran</title>
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
        .status-badge {
            font-weight: bold;
            font-size: 11px;
        }
        .status-completed { color: #16a34a; }
        .status-in-progress { color: #2563eb; }
        .status-not-started { color: #64748b; }
        .status-dropped { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BPSDM LMS</h1>
        <p>Laporan Rekapitulasi Data Pendaftaran</p>
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
            <td><strong>Total Pendaftaran</strong></td>
            <td>: {{ $enrollments->count() }} pendaftaran</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Pengguna</th>
                <th width="15%">NIP</th>
                <th width="30%">Judul Kursus</th>
                <th width="15%" class="text-center">Tgl Daftar</th>
                <th width="15%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $index => $enrollment)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ optional($enrollment->user)->name ?? '-' }}</td>
                <td>{{ optional($enrollment->user)->nip ?? '-' }}</td>
                <td>{{ optional($enrollment->course)->judul ?? '-' }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($enrollment->enrollment_date)->format('d/m/Y') }}</td>
                <td class="text-center">
                    @if($enrollment->status === 'completed')
                        <span class="status-badge status-completed">Selesai</span>
                    @elseif($enrollment->status === 'in_progress')
                        <span class="status-badge status-in-progress">Berjalan</span>
                    @elseif($enrollment->status === 'not_started')
                        <span class="status-badge status-not-started">Belum Mulai</span>
                    @else
                        <span class="status-badge status-dropped">Dibatalkan</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data pendaftaran yang ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem LMS BPSDM. Dokumen ini sah dan tidak memerlukan tanda tangan basah.
    </div>
</body>
</html>
