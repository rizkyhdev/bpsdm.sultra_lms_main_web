<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDF;

class AdminCertificateController extends Controller
{
    /**
     * Membuat instance controller baru.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Menampilkan daftar semua sertifikat yang diterbitkan dengan paginasi dan pencarian.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = Certificate::with(['user', 'course']);

            // Fungsi pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nomor_sertifikat', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('nip', 'like', "%{$search}%");
                      })
                      ->orWhereHas('course', function($courseQuery) use ($search) {
                          $courseQuery->where('judul', 'like', "%{$search}%");
                      });
                });
            }

            // Filter berdasarkan kursus
            if ($request->filled('course_id') && $request->course_id !== 'all') {
                $query->where('course_id', $request->course_id);
            }

            // Filter berdasarkan rentang tanggal penerbitan
            if ($request->filled('date_from')) {
                $query->where('issue_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('issue_date', '<=', $request->date_to);
            }

            $certificates = $query->orderBy('issue_date', 'desc')
                                  ->paginate(15);

            $courses = Course::orderBy('judul')->get();

            return view('admin.certificates.index', compact('certificates', 'courses'));
        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data sertifikat.');
        }
    }

    /**
     * Menampilkan formulir pembuatan sertifikat manual.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $users = User::where('role', 'student')->orderBy('name')->get();
            $courses = Course::orderBy('judul')->get();
            
            return view('admin.certificates.create', compact('users', 'courses'));
        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@create: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat form sertifikat.');
        }
    }

    /**
     * Menyimpan sertifikat yang dibuat secara manual.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'course_id' => 'required|exists:courses,id',
                'nomor_sertifikat' => 'required|string|unique:certificates,nomor_sertifikat|max:100',
                'issue_date' => 'required|date',
                'file_path' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
                'remarks' => 'nullable|string|max:1000'
            ]);

            // Periksa apakah pengguna telah menyelesaikan kursus
            $enrollment = UserEnrollment::where('user_id', $validated['user_id'])
                                       ->where('course_id', $validated['course_id'])
                                       ->where('status', 'completed')
                                       ->first();

            if (!$enrollment) {
                return back()->withInput()->with('error', 'Pengguna belum menyelesaikan kursus ini.');
            }

            // Tangani upload file jika disediakan
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = time() . '_' . Str::slug($validated['nomor_sertifikat']) . '.pdf';
                $filePath = $file->storeAs('certificates', $fileName, 'public');
                $validated['file_path'] = $filePath;
            }

            Certificate::create($validated);

            Log::info('Admin created manual certificate: ' . $validated['nomor_sertifikat']);
            return redirect()->route('admin.certificates.index')
                           ->with('success', 'Sertifikat berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat sertifikat.');
        }
    }

    /**
     * Menampilkan detail sertifikat tertentu.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $certificate = Certificate::with([
                'user',
                'course'
            ])->findOrFail($id);

            return view('admin.certificates.show', compact('certificate'));
        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data sertifikat.');
        }
    }

    /**
     * Menampilkan formulir edit sertifikat.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $certificate = Certificate::with(['user', 'course'])->findOrFail($id);
            $users = User::where('role', 'student')->orderBy('name')->get();
            $courses = Course::orderBy('judul')->get();
            
            return view('admin.certificates.edit', compact('certificate', 'users', 'courses'));
        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@edit: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data sertifikat.');
        }
    }

    /**
     * Memperbarui data sertifikat tertentu.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $oldFilePath = $certificate->file_path;

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'course_id' => 'required|exists:courses,id',
                'nomor_sertifikat' => 'required|string|max:100|unique:certificates,nomor_sertifikat,' . $id,
                'issue_date' => 'required|date',
                'file_path' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
                'remarks' => 'nullable|string|max:1000'
            ]);

            // Tangani upload file jika disediakan
            if ($request->hasFile('file_path')) {
                // Hapus file lama jika ada
                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                $file = $request->file('file_path');
                $fileName = time() . '_' . Str::slug($validated['nomor_sertifikat']) . '.pdf';
                $filePath = $file->storeAs('certificates', $fileName, 'public');
                $validated['file_path'] = $filePath;
            } else {
                // Pertahankan path file yang ada jika tidak ada file baru yang diupload
                unset($validated['file_path']);
            }

            $certificate->update($validated);

            Log::info('Admin updated certificate: ' . $certificate->nomor_sertifikat);
            return redirect()->route('admin.certificates.index')
                           ->with('success', 'Data sertifikat berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data sertifikat.');
        }
    }

    /**
     * Menghapus sertifikat tertentu.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $certificateNumber = $certificate->nomor_sertifikat;

            // Hapus file terkait jika ada
            if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
                Storage::disk('public')->delete($certificate->file_path);
            }

            $certificate->delete();

            Log::info('Admin deleted certificate: ' . $certificateNumber);
            return redirect()->route('admin.certificates.index')
                           ->with('success', 'Sertifikat berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus sertifikat.');
        }
    }

    /**
     * Mengunduh sertifikat PDF.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);

            if (!$certificate->file_path || !Storage::disk('public')->exists($certificate->file_path)) {
                return back()->with('error', 'File sertifikat tidak ditemukan.');
            }

            $fileName = 'certificate_' . $certificate->nomor_sertifikat . '.pdf';
            
            Log::info('Admin downloaded certificate: ' . $certificate->nomor_sertifikat);
            return Storage::disk('public')->download($certificate->file_path, $fileName);

        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@download: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh sertifikat.');
        }
    }

    /**
     * Membuat sertifikat secara massal untuk kursus yang telah selesai.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkGenerate(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'generate_for_all' => 'boolean'
            ]);

            $course = Course::findOrFail($request->course_id);
            $enrollments = UserEnrollment::where('course_id', $request->course_id)
                                        ->where('status', 'completed')
                                        ->with('user');

            if (!$request->has('generate_for_all')) {
                $enrollments = $enrollments->whereDoesntHave('certificates');
            }

            $enrollments = $enrollments->get();

            if ($enrollments->isEmpty()) {
                return back()->with('error', 'Tidak ada peserta yang menyelesaikan kursus ini atau sertifikat sudah ada.');
            }

            $generatedCount = 0;

            DB::transaction(function() use ($enrollments, $course, &$generatedCount) {
                foreach ($enrollments as $enrollment) {
                    // Buat nomor sertifikat
                    $certificateNumber = 'CERT-' . strtoupper(Str::random(8)) . '-' . date('Y');

                    // Periksa apakah nomor sertifikat sudah ada
                    while (Certificate::where('nomor_sertifikat', $certificateNumber)->exists()) {
                        $certificateNumber = 'CERT-' . strtoupper(Str::random(8)) . '-' . date('Y');
                    }

                    Certificate::create([
                        'user_id' => $enrollment->user_id,
                        'course_id' => $course->id,
                        'nomor_sertifikat' => $certificateNumber,
                        'issue_date' => now(),
                        'remarks' => 'Auto-generated from completed enrollment'
                    ]);

                    $generatedCount++;
                }
            });

            Log::info('Admin bulk generated ' . $generatedCount . ' certificates for course: ' . $course->judul);
            return back()->with('success', $generatedCount . ' sertifikat berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@bulkGenerate: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat sertifikat secara massal.');
        }
    }

    /**
     * Memverifikasi keaslian sertifikat.
     *
     * @param string $certificateNumber
     * @return \Illuminate\View\View
     */
    public function verify($certificateNumber)
    {
        try {
            $certificate = Certificate::with(['user', 'course'])
                                     ->where('nomor_sertifikat', $certificateNumber)
                                     ->first();

            if (!$certificate) {
                return view('admin.certificates.verify', [
                    'certificate' => null,
                    'certificateNumber' => $certificateNumber,
                    'isValid' => false
                ]);
            }

            return view('admin.certificates.verify', [
                'certificate' => $certificate,
                'certificateNumber' => $certificateNumber,
                'isValid' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@verify: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memverifikasi sertifikat.');
        }
    }

    /**
     * Membuat PDF sertifikat.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generatePDF($id)
    {
        try {
            $certificate = Certificate::with(['user', 'course'])->findOrFail($id);

            $pdf = PDF::loadView('admin.certificates.pdf', compact('certificate'));
            
            Log::info('Admin generated PDF for certificate: ' . $certificate->nomor_sertifikat);
            return $pdf->download('certificate_' . $certificate->nomor_sertifikat . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@generatePDF: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat PDF sertifikat.');
        }
    }

    /**
     * Dapatkan statistik sertifikat untuk dashboard.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_certificates' => Certificate::count(),
                'certificates_this_month' => Certificate::whereMonth('issue_date', now()->month)->count(),
                'certificates_this_year' => Certificate::whereYear('issue_date', now()->year)->count(),
                'top_courses' => Course::withCount('certificates')
                                      ->orderBy('certificates_count', 'desc')
                                      ->limit(5)
                                      ->get(['id', 'judul', 'certificates_count'])
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error in AdminCertificateController@getStats: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat statistik'], 500);
        }
    }
}
