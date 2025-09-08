<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserEnrollment;
use App\Models\User;
use App\Models\Course;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminEnrollmentController extends Controller
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
     * Menampilkan daftar semua pendaftaran dengan paginasi dan filter.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = UserEnrollment::with(['user', 'course']);

            // Fungsi pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('nip', 'like', "%{$search}%");
                    })
                    ->orWhereHas('course', function($courseQuery) use ($search) {
                        $courseQuery->where('judul', 'like', "%{$search}%");
                    });
                });
            }

            // Filter berdasarkan status
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter berdasarkan kursus
            if ($request->filled('course_id') && $request->course_id !== 'all') {
                $query->where('course_id', $request->course_id);
            }

            // Filter berdasarkan rentang tanggal pendaftaran
            if ($request->filled('date_from')) {
                $query->where('enrollment_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('enrollment_date', '<=', $request->date_to);
            }

            $enrollments = $query->orderBy('enrollment_date', 'desc')
                                 ->paginate(15);

            $courses = Course::orderBy('judul')->get();
            $statuses = ['not_started', 'in_progress', 'completed', 'dropped'];

            return view('admin.enrollments.index', compact('enrollments', 'courses', 'statuses'));
        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pendaftaran.');
        }
    }

    /**
     * Menampilkan formulir pendaftaran.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $users = User::where('role', 'student')->orderBy('name')->get();
            $courses = Course::orderBy('judul')->get();
            
            return view('admin.enrollments.create', compact('users', 'courses'));
        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@create: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat form pendaftaran.');
        }
    }

    /**
     * Mendaftarkan pengguna ke kursus.
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
                'enrollment_date' => 'required|date',
                'status' => 'required|in:not_started,in_progress,completed,dropped',
                'remarks' => 'nullable|string|max:1000'
            ]);

            // Periksa apakah pengguna sudah terdaftar dalam kursus ini
            $existingEnrollment = UserEnrollment::where('user_id', $validated['user_id'])
                                               ->where('course_id', $validated['course_id'])
                                               ->first();

            if ($existingEnrollment) {
                return back()->withInput()->with('error', 'Pengguna sudah terdaftar dalam kursus ini.');
            }

            UserEnrollment::create($validated);

            Log::info('Admin enrolled user ID: ' . $validated['user_id'] . ' to course ID: ' . $validated['course_id']);
            return redirect()->route('admin.enrollments.index')
                           ->with('success', 'Pendaftaran berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat pendaftaran.');
        }
    }

    /**
     * Menampilkan detail pendaftaran dengan progress.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $enrollment = UserEnrollment::with([
                'user',
                'course.modules.subModules.contents',
                'userProgress.subModule'
            ])->findOrFail($id);

            // Hitung statistik progress
            $totalSubModules = $enrollment->course->modules->sum(function($module) {
                return $module->subModules->count();
            });

            $completedSubModules = $enrollment->userProgress->where('is_completed', true)->count();
            $progressPercentage = $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0;

            $progressStats = [
                'total_sub_modules' => $totalSubModules,
                'completed_sub_modules' => $completedSubModules,
                'progress_percentage' => $progressPercentage,
                'remaining_sub_modules' => $totalSubModules - $completedSubModules
            ];

            return view('admin.enrollments.show', compact('enrollment', 'progressStats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pendaftaran.');
        }
    }

    /**
     * Memperbarui status pendaftaran.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $enrollment = UserEnrollment::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:not_started,in_progress,completed,dropped',
                'completed_at' => 'nullable|date',
                'remarks' => 'nullable|string|max:1000'
            ]);

            // Set completed_at jika status adalah completed
            if ($validated['status'] === 'completed' && !$enrollment->completed_at) {
                $validated['completed_at'] = now();
            } elseif ($validated['status'] !== 'completed') {
                $validated['completed_at'] = null;
            }

            $enrollment->update($validated);

            Log::info('Admin updated enrollment ID: ' . $id . ' status to: ' . $validated['status']);
            return redirect()->route('admin.enrollments.index')
                           ->with('success', 'Status pendaftaran berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui status pendaftaran.');
        }
    }

    /**
     * Menghapus pendaftaran.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $enrollment = UserEnrollment::with('userProgress')->findOrFail($id);

            DB::transaction(function() use ($enrollment) {
                // Hapus catatan progress pengguna
                $enrollment->userProgress()->delete();
                
                // Hapus pendaftaran
                $enrollment->delete();
            });

            Log::info('Admin deleted enrollment ID: ' . $id);
            return redirect()->route('admin.enrollments.index')
                           ->with('success', 'Pendaftaran berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus pendaftaran.');
        }
    }

    /**
     * Mendaftarkan pengguna secara massal ke kursus.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkEnroll(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id',
                'enrollment_date' => 'required|date',
                'status' => 'required|in:not_started,in_progress,completed,dropped'
            ]);

            $course = Course::findOrFail($request->course_id);
            $enrolledCount = 0;
            $skippedCount = 0;

            DB::transaction(function() use ($request, &$enrolledCount, &$skippedCount) {
                foreach ($request->user_ids as $userId) {
                    // Periksa apakah pengguna sudah terdaftar
                    $existingEnrollment = UserEnrollment::where('user_id', $userId)
                                                       ->where('course_id', $request->course_id)
                                                       ->first();

                    if ($existingEnrollment) {
                        $skippedCount++;
                        continue;
                    }

                    UserEnrollment::create([
                        'user_id' => $userId,
                        'course_id' => $request->course_id,
                        'enrollment_date' => $request->enrollment_date,
                        'status' => $request->status
                    ]);

                    $enrolledCount++;
                }
            });

            $message = $enrolledCount . ' pengguna berhasil didaftarkan.';
            if ($skippedCount > 0) {
                $message .= ' ' . $skippedCount . ' pengguna dilewati karena sudah terdaftar.';
            }

            Log::info('Admin bulk enrolled ' . $enrolledCount . ' users to course: ' . $course->judul);
            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@bulkEnroll: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mendaftarkan pengguna secara massal.');
        }
    }

    /**
     * Menghasilkan laporan progress.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function progressReport(Request $request)
    {
        try {
            $query = UserEnrollment::with(['user', 'course.modules.subModules']);

            // Filter berdasarkan kursus
            if ($request->filled('course_id') && $request->course_id !== 'all') {
                $query->where('course_id', $request->course_id);
            }

            // Filter berdasarkan status
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $enrollments = $query->get();

            $progressData = $enrollments->map(function($enrollment) {
                $totalSubModules = $enrollment->course->modules->sum(function($module) {
                    return $module->subModules->count();
                });

                $completedSubModules = UserProgress::where('user_id', $enrollment->user_id)
                                                 ->whereHas('subModule', function($query) use ($enrollment) {
                                                     $query->whereHas('module', function($q) use ($enrollment) {
                                                         $q->where('course_id', $enrollment->course_id);
                                                     });
                                                 })
                                                 ->where('is_completed', true)
                                                 ->count();

                $progressPercentage = $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0;

                return [
                    'enrollment' => $enrollment,
                    'total_sub_modules' => $totalSubModules,
                    'completed_sub_modules' => $completedSubModules,
                    'progress_percentage' => $progressPercentage,
                    'remaining_sub_modules' => $totalSubModules - $completedSubModules
                ];
            });

            $courses = Course::orderBy('judul')->get();
            $statuses = ['not_started', 'in_progress', 'completed', 'dropped'];

            // Statistik ringkasan
            $summaryStats = [
                'total_enrollments' => $enrollments->count(),
                'completed_courses' => $enrollments->where('status', 'completed')->count(),
                'in_progress' => $enrollments->where('status', 'in_progress')->count(),
                'not_started' => $enrollments->where('status', 'not_started')->count(),
                'average_progress' => $progressData->avg('progress_percentage')
            ];

            return view('admin.enrollments.progress-report', compact('progressData', 'courses', 'statuses', 'summaryStats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@progressReport: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat laporan progress.');
        }
    }

    /**
     * Mengekspor data pendaftaran ke Excel/PDF.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportEnrollments(Request $request)
    {
        try {
            $query = UserEnrollment::with(['user', 'course']);

            // Terapkan filter
            if ($request->filled('course_id') && $request->course_id !== 'all') {
                $query->where('course_id', $request->course_id);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $enrollments = $query->orderBy('enrollment_date', 'desc')->get();

            $format = $request->get('format', 'xlsx');
            
            if ($format === 'pdf') {
                $pdf = PDF::loadView('admin.enrollments.export-pdf', compact('enrollments'));
                return $pdf->download('enrollments-report.pdf');
            } else {
                return Excel::download(new EnrollmentsExport($enrollments), 'enrollments-report.xlsx');
            }
        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@exportEnrollments: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data pendaftaran.');
        }
    }

    /**
     * Dapatkan statistik pendaftaran untuk dashboard.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_enrollments' => UserEnrollment::count(),
                'enrollments_this_month' => UserEnrollment::whereMonth('enrollment_date', now()->month)->count(),
                'enrollments_this_year' => UserEnrollment::whereYear('enrollment_date', now()->year)->count(),
                'completed_courses' => UserEnrollment::where('status', 'completed')->count(),
                'in_progress' => UserEnrollment::where('status', 'in_progress')->count(),
                'not_started' => UserEnrollment::where('status', 'not_started')->count(),
                'top_courses' => Course::withCount('userEnrollments')
                                      ->orderBy('user_enrollments_count', 'desc')
                                      ->limit(5)
                                      ->get(['id', 'judul', 'user_enrollments_count'])
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@getStats: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat statistik'], 500);
        }
    }

    /**
     * Memperbarui beberapa status pendaftaran sekaligus.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'enrollment_ids' => 'required|array|min:1',
                'enrollment_ids.*' => 'exists:user_enrollments,id',
                'status' => 'required|in:not_started,in_progress,completed,dropped'
            ]);

            $updateData = ['status' => $request->status];
            
            if ($request->status === 'completed') {
                $updateData['completed_at'] = now();
            }

            UserEnrollment::whereIn('id', $request->enrollment_ids)->update($updateData);

            Log::info('Admin bulk updated ' . count($request->enrollment_ids) . ' enrollments to status: ' . $request->status);
            return response()->json(['success' => true, 'message' => 'Status pendaftaran berhasil diperbarui']);

        } catch (\Exception $e) {
            Log::error('Error in AdminEnrollmentController@bulkUpdateStatus: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui status'], 500);
        }
    }
}
