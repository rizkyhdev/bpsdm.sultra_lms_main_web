@extends('layouts.studentapp')

@section('title', "Soal {$nomor} - Simulasi Ujian")

@section('content')
@php
    $total = $attempt->jumlah_soal;
    $terjawab = $answers->count();
    $jawabanSaatIni = $answers->get($question->id);
@endphp

<style>
    .nav-soal { display: grid; grid-template-columns: repeat(auto-fill, minmax(2.75rem, 1fr)); gap: .5rem; }
    .nav-soal .current { outline: 3px solid var(--bs-primary); outline-offset: 2px; }
    .pilihan-jawaban .list-group-item:has(input:checked) { background-color: var(--bs-primary-bg-subtle); border-color: var(--bs-primary-border-subtle); }
</style>

<div class="container-fluid">
    @include('partials._flash')

    {{-- Progres & sisa waktu --}}
    <div class="card shadow-sm border-0 mb-4 sticky-top" style="border-radius: 12px; top: 1rem; z-index: 1010;">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="text-muted small">{{ $attempt->token?->sesi }}</div>
                <h1 class="h5 fw-bold mb-0">Soal {{ $nomor }} dari {{ $total }}</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">Terjawab <strong class="text-body" data-terjawab>{{ $terjawab }}</strong> / {{ $total }}</span>
                <span class="badge bg-primary fs-5 font-monospace" id="timer" role="timer" aria-label="Sisa waktu">--:--</span>
            </div>
        </div>
        <div class="progress rounded-0 rounded-bottom" style="height: 4px;" role="progressbar" aria-label="Progres jawaban"
             aria-valuemin="0" aria-valuemax="{{ $total }}" aria-valuenow="{{ $terjawab }}">
            <div class="progress-bar bg-success" id="progress-bar" style="width: {{ $total ? $terjawab / $total * 100 : 0 }}%"></div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Soal --}}
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <p class="fs-5 mb-4">{!! nl2br(e($question->pertanyaan)) !!}</p>

                    <fieldset>
                        <legend class="visually-hidden">Pilihan jawaban</legend>
                        <div class="list-group pilihan-jawaban">
                            @foreach ($question->options() as $huruf => $teks)
                                <label class="list-group-item list-group-item-action d-flex gap-3 align-items-start py-3" style="cursor: pointer;">
                                    <input class="form-check-input flex-shrink-0 mt-1" type="radio" name="jawaban" value="{{ $huruf }}"
                                           @checked($jawabanSaatIni === $huruf)>
                                    <span><strong class="me-1">{{ $huruf }}.</strong>{{ $teks }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    <div class="small mt-2" id="status-simpan" aria-live="polite"></div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between py-3">
                    @if ($nomor > 1)
                        <a href="{{ route('student.simulasi.exam', [$attempt, $nomor - 1]) }}" class="btn btn-outline-secondary" data-nav>
                            <i class="bi bi-arrow-left me-1"></i>Sebelumnya
                        </a>
                    @else
                        <span></span>
                    @endif

                    @if ($nomor < $total)
                        <a href="{{ route('student.simulasi.exam', [$attempt, $nomor + 1]) }}" class="btn btn-primary" data-nav>
                            Selanjutnya<i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @else
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-selesai">
                            <i class="bi bi-check-circle me-1"></i>Selesai
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Navigasi nomor soal --}}
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h2 class="h6 fw-bold mb-0">Navigasi Soal</h2>
                </div>
                <div class="card-body">
                    <nav class="nav-soal" aria-label="Navigasi nomor soal">
                        @foreach ($attempt->question_ids as $index => $questionId)
                            @php($no = $index + 1)
                            <a href="{{ route('student.simulasi.exam', [$attempt, $no]) }}" data-nav
                               @if ($questionId === $question->id) data-soal-ini aria-current="page" @endif
                               @class([
                                   'btn btn-sm fw-semibold',
                                   'btn-success' => $answers->has($questionId),
                                   'btn-outline-secondary' => ! $answers->has($questionId),
                                   'current' => $no === $nomor,
                               ])>{{ $no }}</a>
                        @endforeach
                    </nav>
                    <div class="d-flex flex-wrap gap-3 small text-muted mt-3">
                        <span><span class="badge bg-success">&nbsp;</span> Sudah dijawab</span>
                        <span><span class="badge border border-secondary">&nbsp;</span> Belum dijawab</span>
                    </div>
                    <button type="button" class="btn btn-success w-100 mt-3" data-bs-toggle="modal" data-bs-target="#modal-selesai">
                        <i class="bi bi-check-circle me-1"></i>Selesaikan Simulasi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="form-selesai" action="{{ route('student.simulasi.finish', $attempt) }}" method="POST" class="d-none">
    @csrf
</form>

<div class="modal fade" id="modal-selesai" tabindex="-1" aria-labelledby="modal-selesai-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="modal-selesai-label">Selesaikan Simulasi?</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Anda telah menjawab <strong data-terjawab>{{ $terjawab }}</strong> dari {{ $total }} soal.</p>
                <p class="text-danger mb-2" id="peringatan-belum" @if ($terjawab >= $total) hidden @endif>
                    Masih ada <strong id="jumlah-belum">{{ $total - $terjawab }}</strong> soal yang belum dijawab.
                </p>
                <p class="text-muted small mb-0">Setelah diselesaikan, jawaban tidak dapat diubah lagi.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" form="form-selesai" class="btn btn-success">Ya, Selesaikan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const answerUrl = @json(route('student.simulasi.answer', $attempt));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const questionId = @json($question->id);
    const total = @json($total);
    // Sisa waktu berasal dari server; hitung mundur memakai jam perangkat agar tidak melenceng.
    const deadline = Date.now() + @json($attempt->remainingSeconds()) * 1000;

    const timerEl = document.getElementById('timer');
    const statusEl = document.getElementById('status-simpan');
    const formSelesai = document.getElementById('form-selesai');

    let queue = Promise.resolve();
    let unsaved = null;
    let finishing = false;

    function setStatus(text, className) {
        statusEl.className = `small mt-2 ${className}`;
        statusEl.textContent = text;
    }

    function updateProgress(terjawab) {
        document.querySelectorAll('[data-terjawab]').forEach((el) => (el.textContent = terjawab));
        document.getElementById('progress-bar').style.width = `${(terjawab / total) * 100}%`;
        document.getElementById('jumlah-belum').textContent = total - terjawab;
        document.getElementById('peringatan-belum').hidden = terjawab >= total;
        const current = document.querySelector('[data-soal-ini]');
        current?.classList.replace('btn-outline-secondary', 'btn-success');
    }

    async function save(jawaban) {
        setStatus('Menyimpan jawaban…', 'text-muted');
        try {
            const response = await fetch(answerUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ question_id: questionId, jawaban }),
            });
            if (response.status === 409) {
                finish('Waktu simulasi telah habis. Menyelesaikan simulasi…');
                return true;
            }
            if (!response.ok) throw new Error(response.statusText);

            updateProgress((await response.json()).terjawab);
            setStatus('Jawaban tersimpan.', 'text-success');
            unsaved = null;
            return true;
        } catch (error) {
            unsaved = jawaban;
            setStatus('Jawaban belum tersimpan. Periksa koneksi internet Anda, lalu pilih ulang jawaban.', 'text-danger');
            return false;
        }
    }

    // Simpan berurutan, sehingga pilihan terakhir selalu yang tercatat.
    const enqueueSave = (jawaban) => (queue = queue.then(() => save(jawaban)));

    function finish(message) {
        if (finishing) return;
        finishing = true;
        clearInterval(timerId);
        if (message) setStatus(message, 'text-danger fw-semibold');
        queue.then(() => formSelesai.submit());
    }

    function tick() {
        const remaining = Math.max(0, Math.round((deadline - Date.now()) / 1000));
        timerEl.textContent = `${Math.floor(remaining / 60)}:${String(remaining % 60).padStart(2, '0')}`;
        if (remaining <= 300) timerEl.classList.replace('bg-primary', 'bg-danger');
        if (remaining === 0) finish('Waktu habis. Jawaban Anda sedang disimpan dan dinilai…');
    }

    const timerId = setInterval(tick, 1000);
    tick();

    document.querySelectorAll('input[name="jawaban"]').forEach((radio) => {
        radio.addEventListener('change', () => enqueueSave(radio.value));
    });

    // Pastikan jawaban tersimpan sebelum berpindah soal.
    document.querySelectorAll('a[data-nav]').forEach((link) => {
        link.addEventListener('click', async (event) => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.button !== 0) return;
            event.preventDefault();
            await queue;
            if (unsaved && !(await enqueueSave(unsaved)) && !confirm('Jawaban soal ini belum tersimpan. Tetap pindah soal?')) return;
            window.location.href = link.href;
        });
    });

    formSelesai.addEventListener('submit', (event) => {
        event.preventDefault();
        finish();
    });
})();
</script>
@endpush
