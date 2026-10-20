<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SimulationQuestionRequest;
use App\Models\SimulationAttempt;
use App\Models\SimulationQuestion;
use Illuminate\Http\Request;

/**
 * Bank Soal untuk Simulasi Uji Kompetensi.
 */
class AdminSimulationQuestionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $questions = SimulationQuestion::query()
            ->withCount('answers')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereLike('pertanyaan', "%{$search}%")
                        ->orWhereLike('pilihan_a', "%{$search}%")
                        ->orWhereLike('pilihan_b', "%{$search}%")
                        ->orWhereLike('pilihan_c', "%{$search}%")
                        ->orWhereLike('pilihan_d', "%{$search}%");
                });
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $totalSoal = SimulationQuestion::count();

        return view('admin.simulasi.soal.index', compact('questions', 'totalSoal', 'search'));
    }

    public function create()
    {
        return view('admin.simulasi.soal.create', ['question' => new SimulationQuestion]);
    }

    public function store(SimulationQuestionRequest $request)
    {
        SimulationQuestion::create($request->validated());

        return redirect()
            ->route('admin.simulasi.soal.index')
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(SimulationQuestion $question)
    {
        $question->loadCount('answers');

        return view('admin.simulasi.soal.edit', compact('question'));
    }

    public function update(SimulationQuestionRequest $request, SimulationQuestion $question)
    {
        $question->update($request->validated());

        return redirect()
            ->route('admin.simulasi.soal.index')
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(SimulationQuestion $question)
    {
        $inUse = $question->answers()->exists()
            || SimulationAttempt::whereNull('completed_at')->whereJsonContains('question_ids', $question->id)->exists();

        if ($inUse) {
            return redirect()
                ->route('admin.simulasi.soal.index')
                ->with('error', 'Soal tidak dapat dihapus karena sudah digunakan dalam simulasi.');
        }

        $question->delete();

        return redirect()
            ->route('admin.simulasi.soal.index')
            ->with('success', 'Soal berhasil dihapus.');
    }
}
