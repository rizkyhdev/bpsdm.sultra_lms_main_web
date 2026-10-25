<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SimulationAttempt;
use App\Models\SimulationQuestion;
use App\Models\SimulationToken;
use App\Services\SimulationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Simulasi Uji Kompetensi (CAT) untuk peserta.
 */
class StudentSimulationController extends Controller
{
    public function __construct(private SimulationService $simulation) {}

    public function index(Request $request)
    {
        $user = $request->user();
        // Hanya pengaturannya yang dibutuhkan; kode token tidak boleh sampai ke halaman peserta.
        $sesi = SimulationToken::active()->latest('id')->first(['jumlah_soal', 'durasi_menit']);
        $bankSoal = SimulationQuestion::count();

        return view('student.simulasi.index', [
            'user' => $user,
            'ongoing' => $this->simulation->ongoingAttemptFor($user),
            'history' => $user->simulationAttempts()
                ->whereNotNull('completed_at')
                ->with('token:id,sesi')
                ->latest('id')
                ->paginate(10),
            'bankSoal' => $bankSoal,
            // Ketentuan sesi yang sedang dibuka admin; null bila belum ada token aktif.
            'jumlahSoal' => $sesi ? min($sesi->jumlah_soal, $bankSoal) : null,
            'durasiMenit' => $sesi?->durasi_menit,
        ]);
    }

    public function start(Request $request)
    {
        $data = $request->validate(
            ['token' => ['required', 'string', 'max:10']],
            ['token.required' => 'Token ujian wajib diisi.', 'token.max' => 'Token ujian tidak valid.'],
        );

        $user = $request->user();

        if ($ongoing = $this->simulation->ongoingAttemptFor($user)) {
            return redirect()
                ->route('student.simulasi.exam', $ongoing)
                ->with('success', 'Anda masih memiliki simulasi yang sedang berlangsung. Silakan lanjutkan.');
        }

        $token = SimulationToken::active()->where('kode', strtoupper(trim($data['token'])))->first();

        if (! $token) {
            return back()->withInput()->withErrors(['token' => 'Token tidak valid atau sudah tidak aktif.']);
        }

        if (! SimulationQuestion::exists()) {
            return back()->with('error', 'Soal belum tersedia. Silakan hubungi admin.');
        }

        $attempt = $this->simulation->start($user, $token);

        return redirect()->route('student.simulasi.exam', $attempt);
    }

    public function exam(SimulationAttempt $attempt, int $nomor = 1)
    {
        Gate::authorize('view', $attempt);

        if ($attempt->isExpired()) {
            $this->simulation->finish($attempt);

            return redirect()
                ->route('student.simulasi.result', $attempt)
                ->with('error', 'Waktu simulasi telah habis. Jawaban Anda telah disimpan dan dinilai.');
        }

        if ($attempt->isFinished()) {
            return redirect()->route('student.simulasi.result', $attempt);
        }

        if ($nomor < 1 || $nomor > $attempt->jumlah_soal) {
            return redirect()->route('student.simulasi.exam', $attempt);
        }

        return view('student.simulasi.exam', [
            'attempt' => $attempt->load('token:id,sesi'),
            'question' => SimulationQuestion::findOrFail($attempt->question_ids[$nomor - 1]),
            'nomor' => $nomor,
            'answers' => $attempt->answers()->pluck('jawaban', 'simulation_question_id'),
        ]);
    }

    public function answer(Request $request, SimulationAttempt $attempt): JsonResponse
    {
        Gate::authorize('update', $attempt);

        $data = $request->validate([
            'question_id' => ['required', 'integer', Rule::in($attempt->question_ids)],
            'jawaban' => ['required', Rule::in(SimulationQuestion::OPTIONS)],
        ]);

        if (! $attempt->acceptsAnswers()) {
            return response()->json(['message' => 'Waktu simulasi telah berakhir.'], 409);
        }

        $this->simulation->saveAnswer($attempt, SimulationQuestion::findOrFail($data['question_id']), $data['jawaban']);

        return response()->json(['terjawab' => $attempt->answers()->count()]);
    }

    public function finish(SimulationAttempt $attempt)
    {
        Gate::authorize('update', $attempt);

        $this->simulation->finish($attempt);

        return redirect()->route('student.simulasi.result', $attempt);
    }

    public function result(SimulationAttempt $attempt)
    {
        Gate::authorize('view', $attempt);

        if (! $attempt->isFinished()) {
            if (! $attempt->isExpired()) {
                return redirect()->route('student.simulasi.exam', $attempt);
            }

            $attempt = $this->simulation->finish($attempt);
        }

        return view('student.simulasi.result', [
            'attempt' => $attempt->load(['token:id,kode,sesi', 'user:id,name,nip,unit_kerja']),
            'terjawab' => $attempt->answers()->count(),
        ]);
    }
}
