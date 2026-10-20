@extends('layouts.admin')

@section('title', 'Simulasi Uji Kompetensi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Simulasi Uji Kompetensi</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.simulasi.soal.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Tambah Soal
    </a>
@endsection

@section('admin_content')
    @include('admin.simulasi._nav')

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Soal di Bank Soal', 'value' => $stats['soal'], 'icon' => 'fa-database', 'color' => 'primary'],
            ['label' => 'Total Peserta', 'value' => $stats['peserta'], 'icon' => 'fa-users', 'color' => 'info'],
            ['label' => 'Sedang Mengerjakan', 'value' => $stats['berlangsung'], 'icon' => 'fa-hourglass-half', 'color' => 'warning'],
            ['label' => 'Selesai', 'value' => $stats['selesai'], 'icon' => 'fa-check-circle', 'color' => 'success'],
        ] as $card)
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }} d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 48px; height: 48px;">
                            <i class="fas {{ $card['icon'] }} fs-5" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <div class="h4 mb-0 fw-bold">{{ number_format($card['value'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        {{-- Token aktif --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title fw-bold"><i class="fas fa-key me-2 text-warning" aria-hidden="true"></i>Token Aktif</h5>
                    @if ($activeToken)
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <span class="display-6 fw-bold font-monospace text-success">{{ $activeToken->kode }}</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-copy-token="{{ $activeToken->kode }}">
                                <i class="far fa-copy me-1" aria-hidden="true"></i><span>Salin</span>
                            </button>
                        </div>
                        <p class="mb-0 mt-2 text-muted">
                            Sesi: <strong class="text-body">{{ $activeToken->sesi }}</strong>
                            &middot; dibuat {{ $activeToken->created_at->format('d M Y H:i') }}
                        </p>
                    @else
                        <p class="text-muted mb-0">Belum ada token aktif. Buat token baru agar peserta dapat memulai simulasi.</p>
                    @endif
                    <hr>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-info-circle me-1" aria-hidden="true"></i>
                        Setiap simulasi berisi {{ config('simulasi.jumlah_soal') }} soal acak dari bank soal
                        dengan waktu pengerjaan {{ config('simulasi.durasi_menit') }} menit.
                    </p>
                </div>
            </div>
        </div>

        {{-- Buat token --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title fw-bold"><i class="fas fa-plus-circle me-2 text-primary" aria-hidden="true"></i>Buat Token Baru</h5>
                    <p class="text-muted small">
                        Token baru langsung aktif dan menonaktifkan token sebelumnya.
                        Bagikan token kepada peserta pada sesi simulasi.
                    </p>
                    <form action="{{ route('admin.simulasi.token.store') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <label for="sesi" class="visually-hidden">Nama sesi</label>
                        <input type="text" id="sesi" name="sesi" value="{{ old('sesi') }}" maxlength="100" required
                               class="form-control @error('sesi') is-invalid @enderror"
                               placeholder="Nama sesi, mis. Simulasi PBJ Gelombang 1">
                        <button type="submit" class="btn btn-primary text-nowrap">Buat Token</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Live score sesi aktif --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold"><i class="fas fa-bolt me-2 text-warning" aria-hidden="true"></i>Live Score</h5>
                <small class="text-muted" id="live-sesi">Sesi aktif</small>
            </div>
            <small class="text-muted" id="live-updated" aria-live="polite"></small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama</th>
                        <th scope="col">NIP</th>
                        <th scope="col">Unit Kerja</th>
                        <th scope="col" class="text-center">Terjawab</th>
                        <th scope="col" class="text-center">Benar</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody id="live-score">
                    <tr><td colspan="7" class="text-center text-muted py-4">Memuat data&hellip;</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat peserta --}}
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary" aria-hidden="true"></i>Daftar Peserta Simulasi</h5>
        </div>
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('admin.simulasi.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-4 col-lg-3">
                    <label for="filter-status" class="form-label small text-muted mb-1">Status</label>
                    <select id="filter-status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="berlangsung" @selected(request('status') === 'berlangsung')>Sedang mengerjakan</option>
                        <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                    </select>
                </div>
                <div class="col-sm-5 col-lg-4">
                    <label for="filter-token" class="form-label small text-muted mb-1">Sesi</label>
                    <select id="filter-token" name="token" class="form-select">
                        <option value="">Semua sesi</option>
                        @foreach ($tokens as $token)
                            <option value="{{ $token->id }}" @selected((string) request('token') === (string) $token->id)>
                                {{ $token->sesi }} ({{ $token->kode }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3 col-lg-auto d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-filter me-1" aria-hidden="true"></i>Terapkan</button>
                    @if (request()->hasAny(['status', 'token']))
                        <a href="{{ route('admin.simulasi.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">NIP</th>
                        <th scope="col">Unit Kerja</th>
                        <th scope="col">Sesi</th>
                        <th scope="col">Mulai</th>
                        <th scope="col">Selesai</th>
                        <th scope="col" class="text-center">Skor</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attempts as $attempt)
                        <tr>
                            <td class="fw-semibold">{{ $attempt->user?->name ?? '-' }}</td>
                            <td>{{ $attempt->user?->nip ?? '-' }}</td>
                            <td>{{ $attempt->user?->unit_kerja ?? '-' }}</td>
                            <td>
                                {{ $attempt->token?->sesi }}
                                <div class="small text-muted font-monospace">{{ $attempt->token?->kode }}</div>
                            </td>
                            <td class="text-nowrap">{{ $attempt->started_at->format('d M Y H:i') }}</td>
                            <td class="text-nowrap">{{ $attempt->completed_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="text-center text-nowrap">
                                @if ($attempt->isFinished())
                                    <span class="fw-bold text-primary">{{ $attempt->skor }}</span>
                                    <span class="text-muted small">/ {{ $attempt->jumlah_soal }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>@include('admin.simulasi._status-badge', ['status' => $attempt->status_label])</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada peserta simulasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($attempts->hasPages())
            <div class="card-body">{{ $attempts->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const liveUrl = @json(route('admin.simulasi.live'));
    const tbody = document.getElementById('live-score');
    const sesiEl = document.getElementById('live-sesi');
    const updatedEl = document.getElementById('live-updated');
    const statusClass = {
        'Selesai': 'bg-success',
        'Sedang Mengerjakan': 'bg-warning text-dark',
        'Waktu Habis': 'bg-secondary',
    };

    // Data peserta dirender lewat textContent agar aman dari XSS.
    const cell = (text, className = '') => {
        const td = document.createElement('td');
        td.textContent = text ?? '-';
        if (className) td.className = className;
        return td;
    };

    const messageRow = (text) => {
        const tr = document.createElement('tr');
        const td = cell(text, 'text-center text-muted py-4');
        td.colSpan = 7;
        tr.append(td);
        return tr;
    };

    const pesertaRow = (p, index) => {
        const tr = document.createElement('tr');
        const status = document.createElement('td');
        const badge = document.createElement('span');
        badge.className = 'badge ' + (statusClass[p.status] ?? 'bg-secondary');
        badge.textContent = p.status;
        status.append(badge);
        tr.append(
            cell(index + 1),
            cell(p.nama, 'fw-semibold'),
            cell(p.nip),
            cell(p.unit_kerja),
            cell(`${p.terjawab} / ${p.jumlah_soal}`, 'text-center'),
            cell(p.benar, 'text-center fw-bold text-primary'),
            status,
        );
        return tr;
    };

    async function refresh() {
        try {
            const response = await fetch(liveUrl, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error(response.statusText);
            const data = await response.json();

            sesiEl.textContent = data.sesi ? `Sesi: ${data.sesi} (token ${data.kode})` : 'Tidak ada sesi aktif';
            tbody.replaceChildren(...(data.peserta.length
                ? data.peserta.map(pesertaRow)
                : [messageRow(data.sesi ? 'Belum ada peserta pada sesi ini.' : 'Buat token untuk memulai sesi simulasi.')]));
            updatedEl.textContent = 'Diperbarui ' + new Date().toLocaleTimeString('id-ID');
        } catch (error) {
            updatedEl.textContent = 'Gagal memuat live score, mencoba lagi…';
        }
    }

    refresh();
    setInterval(() => document.visibilityState === 'visible' && refresh(), 5000);

    document.querySelectorAll('[data-copy-token]').forEach((button) => {
        button.addEventListener('click', async () => {
            const label = button.querySelector('span');
            try {
                await navigator.clipboard.writeText(button.dataset.copyToken);
                label.textContent = 'Tersalin';
            } catch (error) {
                label.textContent = 'Gagal menyalin';
            }
            setTimeout(() => (label.textContent = 'Salin'), 2000);
        });
    });
})();
</script>
@endpush
