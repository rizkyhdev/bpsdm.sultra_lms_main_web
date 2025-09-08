<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\UserEnrollment;
use App\Models\QuizAttempt;
use App\Models\Certificate;
use App\Models\JpRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class AdminReportController extends Controller
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
     * Dashboard admin dengan metrik utama.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        try {
            // Statistik pengguna
            $userStats = [
                'total_users' => User::count(),
                'total_students' => User::where('role', 'student')->count(),
                'total_instructors' => User::where('role', 'instructor')->count(),
                'total_admins' => User::where('role', 'admin')->count(),
                'validated_users' => User::where('is_validated', true)->count(),
                'pending_validation' => User::where('is_validated', false)->count(),
                'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'new_users_this_year' => User::whereYear('created_at', now()->year)->count()
            ];

            // Statistik kursus
            $courseStats = [
                'total_courses' => Course::count(),
                'active_courses' => Course::where('is_active', true)->count(),
                'total_modules' => DB::table('modules')->count(),
                'total_sub_modules' => DB::table('sub_modules')->count(),
                'total_contents' => DB::table('contents')->count(),
                'total_quizzes' => DB::table('quizzes')->count(),
                'courses_this_month' => Course::whereMonth('created_at', now()->month)->count(),
                'courses_this_year' => Course::whereYear('created_at', now()->year)->count()
            ];

            // Statistik pendaftaran
            $enrollmentStats = [
                'total_enrollments' => UserEnrollment::count(),
                'active_enrollments' => UserEnrollment::where('status', 'in_progress')->count(),
                'completed_enrollments' => UserEnrollment::where('status', 'completed')->count(),
                'enrollments_this_month' => UserEnrollment::whereMonth('enrollment_date', now()->month)->count(),
                'enrollments_this_year' => UserEnrollment::whereYear('enrollment_date', now()->year)->count(),
                'completion_rate' => UserEnrollment::count() > 0 
                    ? round((UserEnrollment::where('status', 'completed')->count() / UserEnrollment::count()) * 100, 2)
                    : 0
            ];

            // Statistik kuis
            $quizStats = [
                'total_quiz_attempts' => QuizAttempt::count(),
                'passed_attempts' => QuizAttempt::where('is_passed', true)->count(),
                'failed_attempts' => QuizAttempt::where('is_passed', false)->count(),
                'average_score' => QuizAttempt::count() > 0 
                    ? round(QuizAttempt::avg('nilai'), 2) 
                    : 0,
                'attempts_this_month' => QuizAttempt::whereMonth('created_at', now()->month)->count(),
                'pass_rate' => QuizAttempt::count() > 0 
                    ? round((QuizAttempt::where('is_passed', true)->count() / QuizAttempt::count()) * 100, 2)
                    : 0
            ];

            // Statistik sertifikat
            $certificateStats = [
                'total_certificates' => Certificate::count(),
                'certificates_this_month' => Certificate::whereMonth('issue_date', now()->month)->count(),
                'certificates_this_year' => Certificate::whereYear('issue_date', now()->year)->count()
            ];

            // Statistik JP
            $jpStats = [
                'total_jp_earned' => JpRecord::sum('jp_earned'),
                'jp_this_year' => JpRecord::whereYear('tahun', now()->year)->sum('jp_earned'),
                'average_jp_per_user' => User::where('role', 'student')->count() > 0 
                    ? round(JpRecord::sum('jp_earned') / User::where('role', 'student')->count(), 2)
                    : 0
            ];

            // Aktivitas terbaru
            $recentActivities = [
                'latest_users' => User::orderBy('created_at', 'desc')->limit(5)->get(),
                'latest_courses' => Course::orderBy('created_at', 'desc')->limit(5)->get(),
                'latest_enrollments' => UserEnrollment::with('user', 'course')
                                                     ->orderBy('enrollment_date', 'desc')
                                                     ->limit(5)
                                                     ->get(),
                'latest_certificates' => Certificate::with('user', 'course')
                                                   ->orderBy('issue_date', 'desc')
                                                   ->limit(5)
                                                   ->get()
            ];

            // Kursus dengan performa terbaik
            $topCourses = Course::withCount('userEnrollments')
                                ->orderBy('user_enrollments_count', 'desc')
                                ->limit(10)
                                ->get();

            // Tren bulanan (12 bulan terakhir)
            $monthlyTrends = $this->getMonthlyTrends();

            return view('admin.dashboard', compact(
                'userStats',
                'courseStats',
                'enrollmentStats',
                'quizStats',
                'certificateStats',
                'jpStats',
                'recentActivities',
                'topCourses',
                'monthlyTrends'
            ));

        } catch (\Exception $e) {
            Log::error('Error in AdminReportController@dashboard: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat dashboard.');
        }
    }

    /**
     * Menghasilkan laporan aktivitas pengguna.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function userReport(Request $request)
    {
        try {
            $query = User::withCount(['userEnrollments', 'quizAttempts', 'certificates']);

            // Filter berdasarkan peran
            if ($request->filled('role') && $request->role !== 'all') {
                $query->where('role', $request->role);
            }

            // Filter berdasarkan status validasi
            if ($request->filled('is_validated')) {
                $query->where('is_validated', $request->is_validated);
            }

            // Filter berdasarkan rentang tanggal pendaftaran
            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            $users = $query->orderBy('created_at', 'desc')->paginate(20);

            $roles = ['admin', 'instructor', 'student'];
            $validationStatuses = [true, false];

            // Statistik ringkasan
            $summaryStats = [
                'total_users' => User::count(),
                'active_users' => User::where('is_validated', true)->count(),
                'pending_users' => User::where('is_validated', false)->count(),
                'users_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'users_this_year' => User::whereYear('created_at', now()->year)->count()
            ];

            return view('admin.reports.user-report', compact('users', 'roles', 'validationStatuses', 'summaryStats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminReportController@userReport: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat laporan pengguna.');
        }
    }

    /**
     * Menghasilkan laporan penyelesaian kursus.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function courseReport(Request $request)
    {
        try {
            $query = Course::withCount(['modules', 'subModules', 'userEnrollments']);

            // Filter berdasarkan bidang kompetensi
            if ($request->filled('bidang_kompetensi') && $request->bidang_kompetensi !== 'all') {
                $query->where('bidang_kompetensi', $request->bidang_kompetensi);
            }

            // Filter berdasarkan rentang nilai JP
            if ($request->filled('jp_min')) {
                $query->where('jp_value', '>=', $request->jp_min);
            }

            if ($request->filled('jp_max')) {
                $query->where('jp_value', '<=', $request->jp_max);
            }

            $courses = $query->orderBy('created_at', 'desc')->get();

            $courseStats = $courses->map(function($course) {
                $completedEnrollments = UserEnrollment::where('course_id', $course->id)
                                                     ->where('status', 'completed')
                                                     ->count();

                $inProgressEnrollments = UserEnrollment::where('course_id', $course->id)
                                                      ->where('status', 'in_progress')
                                                      ->count();

                $completionRate = $course->userEnrollments->count() > 0 
                    ? round(($completedEnrollments / $course->userEnrollments->count()) * 100, 2)
                    : 0;

                return [
                    'course' => $course,
                    'completed_enrollments' => $completedEnrollments,
                    'in_progress_enrollments' => $inProgressEnrollments,
                    'completion_rate' => $completionRate,
                    'average_progress' => $this->getAverageCourseProgress($course->id)
                ];
            });

            $bidangKompetensi = Course::distinct()->pluck('bidang_kompetensi');

            // Statistik ringkasan
            $summaryStats = [
                'total_courses' => $courses->count(),
                'total_enrollments' => $courses->sum('user_enrollments_count'),
                'average_completion_rate' => $courseStats->avg('completion_rate'),
                'courses_this_month' => $courses->where('created_at', '>=', now()->startOfMonth())->count()
            ];

            return view('admin.reports.course-report', compact('courseStats', 'bidangKompetensi', 'summaryStats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminReportController@courseReport: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat laporan kursus.');
        }
    }

    /**
     * Menghasilkan laporan akumulasi JP.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function jpReport(Request $request)
    {
        try {
            $query = JpRecord::with(['user', 'course']);

            // Filter berdasarkan tahun
            if ($request->filled('year') && $request->year !== 'all') {
                $query->where('tahun', $request->year);
            }

            // Filter berdasarkan kursus
            if ($request->filled('course_id') && $request->course_id !== 'all') {
                $query->where('course_id', $request->course_id);
            }

            $jpRecords = $query->orderBy('recorded_at', 'desc')->get();

            // Kelompokkan berdasarkan pengguna untuk ringkasan
            $userJpSummary = $jpRecords->groupBy('user_id')->map(function($records, $userId) {
                $user = $records->first()->user;
                $totalJp = $records->sum('jp_earned');
                $coursesCount = $records->count();
                $yearlyBreakdown = $records->groupBy('tahun')->map(function($yearRecords) {
                    return $yearRecords->sum('jp_earned');
                });

                return [
                    'user' => $user,
                    'total_jp' => $totalJp,
                    'courses_count' => $coursesCount,
                    'yearly_breakdown' => $yearlyBreakdown,
                    'records' => $records
                ];
            })->sortByDesc('total_jp');

            $years = JpRecord::distinct()->pluck('tahun')->sort();
            $courses = Course::orderBy('judul')->get();

            // Statistik ringkasan
            $summaryStats = [
                'total_jp_earned' => $jpRecords->sum('jp_earned'),
                'total_records' => $jpRecords->count(),
                'unique_users' => $userJpSummary->count(),
                'average_jp_per_user' => $userJpSummary->count() > 0 
                    ? round($jpRecords->sum('jp_earned') / $userJpSummary->count(), 2)
                    : 0,
                'jp_this_year' => $jpRecords->where('tahun', now()->year)->sum('jp_earned')
            ];

            return view('admin.reports.jp-report', compact('userJpSummary', 'years', 'courses', 'summaryStats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminReportController@jpReport: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat laporan JP.');
        }
    }

    /**
     * Menghasilkan laporan kinerja kuis.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function quizReport(Request $request)
    {
        try {
            $query = QuizAttempt::with(['user', 'quiz.subModule.module.course']);

            // Filter berdasarkan rentang tanggal
            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            // Filter berdasarkan kursus
            if ($request->filled('course_id') && $request->course_id !== 'all') {
                $query->whereHas('quiz.subModule.module', function($q) use ($request) {
                    $q->where('course_id', $request->course_id);
                });
            }

            $quizAttempts = $query->orderBy('created_at', 'desc')->get();

            // Kelompokkan berdasarkan kuis untuk analisis
            $quizAnalysis = $quizAttempts->groupBy('quiz_id')->map(function($attempts, $quizId) {
                $quiz = $attempts->first()->quiz;
                $totalAttempts = $attempts->count();
                $passedAttempts = $attempts->where('is_passed', true)->count();
                $averageScore = $attempts->avg('nilai');
                $highestScore = $attempts->max('nilai');
                $lowestScore = $attempts->min('nilai');

                return [
                    'quiz' => $quiz,
                    'total_attempts' => $totalAttempts,
                    'passed_attempts' => $passedAttempts,
                    'failed_attempts' => $totalAttempts - $passedAttempts,
                    'pass_rate' => $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 2) : 0,
                    'average_score' => round($averageScore, 2),
                    'highest_score' => $highestScore,
                    'lowest_score' => $lowestScore
                ];
            })->sortByDesc('total_attempts');

            $courses = Course::orderBy('judul')->get();

            // Statistik ringkasan
            $summaryStats = [
                'total_attempts' => $quizAttempts->count(),
                'passed_attempts' => $quizAttempts->where('is_passed', true)->count(),
                'failed_attempts' => $quizAttempts->where('is_passed', false)->count(),
                'overall_pass_rate' => $quizAttempts->count() > 0 
                    ? round(($quizAttempts->where('is_passed', true)->count() / $quizAttempts->count()) * 100, 2)
                    : 0,
                'average_score' => round($quizAttempts->avg('nilai'), 2),
                'unique_users' => $quizAttempts->unique('user_id')->count()
            ];

            return view('admin.reports.quiz-report', compact('quizAnalysis', 'courses', 'summaryStats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminReportController@quizReport: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat laporan kuis.');
        }
    }

    /**
     * Menghasilkan laporan penerbitan sertifikat.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function certificateReport(Request $request)
    {
        try {
            $query = Certificate::with(['user', 'course']);

            // Filter berdasarkan rentang tanggal penerbitan
            if ($request->filled('date_from')) {
                $query->where('issue_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('issue_date', '<=', $request->date_to);
            }

            // Filter berdasarkan kursus
            if ($request->filled('course_id') && $request->course_id !== 'all') {
                $query->where('course_id', $request->course_id);
            }

            $certificates = $query->orderBy('issue_date', 'desc')->get();

            // Kelompokkan berdasarkan kursus untuk analisis
            $courseAnalysis = $certificates->groupBy('course_id')->map(function($certs, $courseId) {
                $course = $certs->first()->course;
                $totalCertificates = $certs->count();
                $thisMonth = $certs->where('issue_date', '>=', now()->startOfMonth())->count();
                $thisYear = $certs->where('issue_date', '>=', now()->startOfYear())->count();

                return [
                    'course' => $course,
                    'total_certificates' => $totalCertificates,
                    'this_month' => $thisMonth,
                    'this_year' => $thisYear
                ];
            })->sortByDesc('total_certificates');

            // Rincian bulanan
            $monthlyBreakdown = $certificates->groupBy(function($cert) {
                return Carbon::parse($cert->issue_date)->format('Y-m');
            })->map(function($monthCerts) {
                return $monthCerts->count();
            })->sortKeys();

            $courses = Course::orderBy('judul')->get();

            // Statistik ringkasan
            $summaryStats = [
                'total_certificates' => $certificates->count(),
                'certificates_this_month' => $certificates->where('issue_date', '>=', now()->startOfMonth())->count(),
                'certificates_this_year' => $certificates->where('issue_date', '>=', now()->startOfYear())->count(),
                'unique_users' => $certificates->unique('user_id')->count(),
                'unique_courses' => $certificates->unique('course_id')->count()
            ];

            return view('admin.reports.certificate-report', compact('courseAnalysis', 'monthlyBreakdown', 'courses', 'summaryStats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminReportController@certificateReport: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat laporan sertifikat.');
        }
    }

    /**
     * Mengekspor laporan ke Excel/PDF.
     *
     * @param Request $request
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportReport(Request $request, $type)
    {
        try {
            $format = $request->get('format', 'xlsx');
            
            switch ($type) {
                case 'users':
                    $data = $this->getUserReportData($request);
                    $view = 'admin.reports.exports.users';
                    $filename = 'users-report';
                    break;
                    
                case 'courses':
                    $data = $this->getCourseReportData($request);
                    $view = 'admin.reports.exports.courses';
                    $filename = 'courses-report';
                    break;
                    
                case 'enrollments':
                    $data = $this->getEnrollmentReportData($request);
                    $view = 'admin.reports.exports.enrollments';
                    $filename = 'enrollments-report';
                    break;
                    
                case 'quizzes':
                    $data = $this->getQuizReportData($request);
                    $view = 'admin.reports.exports.quizzes';
                    $filename = 'quizzes-report';
                    break;
                    
                case 'certificates':
                    $data = $this->getCertificateReportData($request);
                    $view = 'admin.reports.exports.certificates';
                    $filename = 'certificates-report';
                    break;
                    
                case 'jp':
                    $data = $this->getJpReportData($request);
                    $view = 'admin.reports.exports.jp';
                    $filename = 'jp-report';
                    break;
                    
                default:
                    return back()->with('error', 'Tipe laporan tidak valid.');
            }

            if ($format === 'pdf') {
                $pdf = PDF::loadView($view, compact('data'));
                return $pdf->download($filename . '.pdf');
            } else {
                return Excel::download(new ReportExport($data, $type), $filename . '.xlsx');
            }

        } catch (\Exception $e) {
            Log::error('Error in AdminReportController@exportReport: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengekspor laporan.');
        }
    }

    /**
     * Dapatkan tren bulanan untuk dashboard.
     *
     * @return array
     */
    private function getMonthlyTrends()
    {
        $trends = [];
        $startDate = now()->subMonths(11)->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M Y');

            $trends[$monthKey] = [
                'label' => $monthLabel,
                'users' => User::whereMonth('created_at', $date->month)
                               ->whereYear('created_at', $date->year)
                               ->count(),
                'enrollments' => UserEnrollment::whereMonth('enrollment_date', $date->month)
                                              ->whereYear('enrollment_date', $date->year)
                                              ->count(),
                'certificates' => Certificate::whereMonth('issue_date', $date->month)
                                            ->whereYear('issue_date', $date->year)
                                            ->count(),
                'jp_earned' => JpRecord::whereMonth('recorded_at', $date->month)
                                       ->whereYear('recorded_at', $date->year)
                                       ->sum('jp_earned')
            ];
        }

        return $trends;
    }

    /**
     * Dapatkan rata-rata progress kursus.
     *
     * @param int $courseId
     * @return float
     */
    private function getAverageCourseProgress($courseId)
    {
        $enrollments = UserEnrollment::where('course_id', $courseId)->get();
        
        if ($enrollments->isEmpty()) {
            return 0;
        }

        $totalProgress = 0;
        foreach ($enrollments as $enrollment) {
            $totalSubModules = DB::table('sub_modules')
                ->join('modules', 'sub_modules.module_id', '=', 'modules.id')
                ->where('modules.course_id', $courseId)
                ->count();

            $completedSubModules = DB::table('user_progress')
                ->join('sub_modules', 'user_progress.sub_module_id', '=', 'sub_modules.id')
                ->join('modules', 'sub_modules.module_id', '=', 'modules.id')
                ->where('user_progress.user_id', $enrollment->user_id)
                ->where('modules.course_id', $courseId)
                ->where('user_progress.is_completed', true)
                ->count();

            $progress = $totalSubModules > 0 ? ($completedSubModules / $totalSubModules) * 100 : 0;
            $totalProgress += $progress;
        }

        return round($totalProgress / $enrollments->count(), 2);
    }

    /**
     * Dapatkan data laporan pengguna untuk ekspor.
     *
     * @param Request $request
     * @return Collection
     */
    private function getUserReportData($request)
    {
        $query = User::withCount(['userEnrollments', 'quizAttempts', 'certificates']);
        
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        
        return $query->get();
    }

    /**
     * Dapatkan data laporan kursus untuk ekspor.
     *
     * @param Request $request
     * @return Collection
     */
    private function getCourseReportData($request)
    {
        return Course::withCount(['modules', 'subModules', 'userEnrollments'])->get();
    }

    /**
     * Dapatkan data laporan pendaftaran untuk ekspor.
     *
     * @param Request $request
     * @return Collection
     */
    private function getEnrollmentReportData($request)
    {
        $query = UserEnrollment::with(['user', 'course']);
        
        if ($request->filled('course_id') && $request->course_id !== 'all') {
            $query->where('course_id', $request->course_id);
        }
        
        return $query->get();
    }

    /**
     * Dapatkan data laporan kuis untuk ekspor.
     *
     * @param Request $request
     * @return Collection
     */
    private function getQuizReportData($request)
    {
        $query = QuizAttempt::with(['user', 'quiz.subModule.module.course']);
        
        if ($request->filled('course_id') && $request->course_id !== 'all') {
            $query->whereHas('quiz.subModule.module', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        
        return $query->get();
    }

    /**
     * Dapatkan data laporan sertifikat untuk ekspor.
     *
     * @param Request $request
     * @return Collection
     */
    private function getCertificateReportData($request)
    {
        $query = Certificate::with(['user', 'course']);
        
        if ($request->filled('course_id') && $request->course_id !== 'all') {
            $query->where('course_id', $request->course_id);
        }
        
        return $query->get();
    }

    /**
     * Dapatkan data laporan JP untuk ekspor.
     *
     * @param Request $request
     * @return Collection
     */
    private function getJpReportData($request)
    {
        $query = JpRecord::with(['user', 'course']);
        
        if ($request->filled('year') && $request->year !== 'all') {
            $query->where('tahun', $request->year);
        }
        
        return $query->get();
    }
}
