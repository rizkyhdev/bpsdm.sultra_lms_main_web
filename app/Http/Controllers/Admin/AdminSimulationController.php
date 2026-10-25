<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SimulationTokenRequest;
use App\Models\SimulationAttempt;
use App\Models\SimulationQuestion;
use App\Models\SimulationToken;
use App\Services\SimulationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Monitoring Simulasi Uji Kompetensi: token sesi, live score, dan riwayat peserta.
 */
class AdminSimulationController extends Controller
{
    public function __construct(private SimulationService $simulation) {}

    public function index(Request $request)
    {
        $this->simulation->finishExpired();

        $status = $request->query('status');
        $tokenId = $request->query('token');

        $attempts = SimulationAttempt::query()
            ->with(['user:id,name,nip,unit_kerja', 'token:id,kode,sesi'])
            ->when($status === 'berlangsung', fn ($q) => $q->whereNull('completed_at'))
            ->when($status === 'selesai', fn ($q) => $q->whereNotNull('completed_at'))
            ->when($tokenId, fn ($q) => $q->where('simulation_token_id', $tokenId))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'soal' => SimulationQuestion::count(),
            'peserta' => SimulationAttempt::count(),
            'berlangsung' => SimulationAttempt::whereNull('completed_at')->count(),
            'selesai' => SimulationAttempt::whereNotNull('completed_at')->count(),
        ];

        return view('admin.simulasi.index', [
            'activeToken' => SimulationToken::active()->latest('id')->first(),
            'tokens' => SimulationToken::latest('id')->get(['id', 'kode', 'sesi']),
            'attempts' => $attempts,
            'stats' => $stats,
        ]);
    }

    public function storeToken(SimulationTokenRequest $request)
    {
        $token = $this->simulation->generateToken(
            $request->validated('sesi'),
            $request->integer('jumlah_soal'),
            $request->integer('durasi_menit'),
        );

        return redirect()
            ->route('admin.simulasi.index')
            ->with('success', "Token baru berhasil dibuat: {$token->kode} ({$token->jumlah_soal} soal, {$token->durasi_menit} menit)");
    }

    /**
     * Live score peserta pada sesi (token) yang sedang aktif.
     */
    public function live(): JsonResponse
    {
        $this->simulation->finishExpired();

        $token = SimulationToken::active()->latest('id')->first();

        if (! $token) {
            return response()->json(['sesi' => null, 'peserta' => []]);
        }

        $peserta = $token->attempts()
            ->with('user:id,name,nip,unit_kerja')
            ->withCount(['answers', 'answers as benar_count' => fn ($q) => $q->where('is_correct', true)])
            ->orderByDesc('benar_count')
            ->orderBy('id')
            ->get()
            ->map(fn (SimulationAttempt $attempt) => [
                'nama' => $attempt->user?->name,
                'nip' => $attempt->user?->nip,
                'unit_kerja' => $attempt->user?->unit_kerja,
                'terjawab' => $attempt->answers_count,
                'benar' => $attempt->benar_count,
                'jumlah_soal' => $attempt->jumlah_soal,
                'status' => $attempt->status_label,
            ]);

        return response()->json([
            'sesi' => $token->sesi,
            'kode' => $token->kode,
            'peserta' => $peserta,
        ]);
    }
}
