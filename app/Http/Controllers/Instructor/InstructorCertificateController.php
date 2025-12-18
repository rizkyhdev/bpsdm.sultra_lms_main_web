<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstructorCertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    /**
     * Tampilkan daftar sertifikat untuk peserta pada pelatihan milik instruktur.
     */
    public function index(Request $request)
    {
        try {
            $instructorId = Auth::id();

            $query = Certificate::with(['user', 'course'])
                ->whereHas('course', function ($q) use ($instructorId) {
                    $q->where('user_id', $instructorId);
                });

            if ($request->filled('search')) {
                $search = $request->string('search')->toString();
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_sertifikat', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                      ->orWhere('nip', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('course', function ($courseQuery) use ($search) {
                            $courseQuery->where('judul', 'like', "%{$search}%");
                        });
                });
            }

            $perPage = (int) $request->input('per_page', 15);
            if ($perPage <= 0) {
                $perPage = 15;
            }

            $certificates = $query
                ->orderByDesc('issue_date')
                ->paginate($perPage)
                ->withQueryString();

            return view('instructor.certificates.index', compact('certificates'));
        } catch (\Throwable $e) {
            Log::error('Error in InstructorCertificateController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memuat data sertifikat instruktur.');
        }
    }
}

