<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\JpRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StudentJPRecordController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Display a listing of all JP records.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        $query = $user->jpRecords()
            ->with(['course'])
            ->orderBy('created_at', 'desc');

        // Filter by year if provided
        if ($request->has('year') && $request->year !== '') {
            $query->whereYear('created_at', $request->year);
        }

        // Filter by course if provided
        if ($request->has('course_id') && $request->course_id !== '') {
            $query->where('course_id', $request->course_id);
        }

        // Filter by bidang kompetensi if provided
        if ($request->has('bidang_kompetensi') && $request->bidang_kompetensi !== '') {
            $query->whereHas('course', function ($q) use ($request) {
                $q->where('bidang_kompetensi', $request->bidang_kompetensi);
            });
        }

        $jpRecords = $query->paginate(15);
        
        // Get available years for filtering
        $availableYears = $user->jpRecords()
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year')
            ->sort()
            ->reverse();

        // Get available courses for filtering
        $availableCourses = $user->jpRecords()
            ->with('course:id,judul,bidang_kompetensi')
            ->get()
            ->pluck('course')
            ->unique('id')
            ->filter();

        // Get available bidang kompetensi for filtering
        $availableBidangKompetensi = $user->jpRecords()
            ->with('course:id,bidang_kompetensi')
            ->get()
            ->pluck('course.bidang_kompetensi')
            ->unique()
            ->filter();

        // Get JP statistics
        $totalJpEarned = $user->jpRecords()->sum('jp_value');
        $jpThisYear = $user->jpRecords()
            ->whereYear('created_at', now()->year)
            ->sum('jp_value');
        $totalCoursesCompleted = $user->jpRecords()->count();

        return view('student.jp-records.index', compact(
            'jpRecords',
            'availableYears',
            'availableCourses',
            'availableBidangKompetensi',
            'totalJpEarned',
            'jpThisYear',
            'totalCoursesCompleted'
        ));
    }

    /**
     * Display JP summary for the current year.
     */
    public function yearSummary(Request $request): View
    {
        $user = Auth::user();
        $year = $request->get('year', now()->year);
        
        // Get JP records for the specified year
        $jpRecords = $user->jpRecords()
            ->whereYear('created_at', $year)
            ->with(['course'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate monthly JP summary
        $monthlyJp = [];
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthJp = $user->jpRecords()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('jp_value');
            
            $monthlyJp[$month] = $monthJp;
            
            $monthlyData[] = [
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'jp_value' => $monthJp
            ];
        }

        // Get JP by bidang kompetensi
        $jpByBidangKompetensi = $user->jpRecords()
            ->whereYear('created_at', $year)
            ->with('course:id,bidang_kompetensi')
            ->get()
            ->groupBy('course.bidang_kompetensi')
            ->map(function ($group) {
                return [
                    'bidang_kompetensi' => $group->first()->course->bidang_kompetensi,
                    'total_jp' => $group->sum('jp_value'),
                    'course_count' => $group->count()
                ];
            })
            ->sortByDesc('total_jp')
            ->values();

        // Get JP by course
        $jpByCourse = $user->jpRecords()
            ->whereYear('created_at', $year)
            ->with('course:id,judul,bidang_kompetensi')
            ->orderBy('jp_value', 'desc')
            ->get();

        // Calculate totals
        $totalJpYear = array_sum($monthlyJp);
        $totalCoursesYear = $jpRecords->count();
        $averageJpPerCourse = $totalCoursesYear > 0 ? round($totalJpYear / $totalCoursesYear, 2) : 0;

        // Get year comparison
        $previousYear = $year - 1;
        $previousYearJp = $user->jpRecords()
            ->whereYear('created_at', $previousYear)
            ->sum('jp_value');
        
        $jpChange = $previousYearJp > 0 ? round((($totalJpYear - $previousYearJp) / $previousYearJp) * 100, 2) : 0;

        // Get target achievement (assuming 40 JP per year target)
        $targetJp = 40;
        $targetAchievement = $targetJp > 0 ? round(($totalJpYear / $targetJp) * 100, 2) : 0;

        return view('student.jp-records.year-summary', compact(
            'year',
            'jpRecords',
            'monthlyJp',
            'monthlyData',
            'jpByBidangKompetensi',
            'jpByCourse',
            'totalJpYear',
            'totalCoursesYear',
            'averageJpPerCourse',
            'previousYearJp',
            'jpChange',
            'targetJp',
            'targetAchievement'
        ));
    }

    /**
     * Get JP data for AJAX requests.
     */
    public function getJpData(Request $request): JsonResponse
    {
        $user = Auth::user();
        $year = $request->get('year', now()->year);
        
        // Get JP records for the specified year
        $jpRecords = $user->jpRecords()
            ->whereYear('created_at', $year)
            ->with(['course:id,judul,bidang_kompetensi'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate monthly JP summary
        $monthlyJp = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyJp[$month] = $user->jpRecords()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('jp_value');
        }

        // Get JP by bidang kompetensi
        $jpByBidangKompetensi = $user->jpRecords()
            ->whereYear('created_at', $year)
            ->with('course:id,bidang_kompetensi')
            ->get()
            ->groupBy('course.bidang_kompetensi')
            ->map(function ($group) {
                return [
                    'bidang_kompetensi' => $group->first()->course->bidang_kompetensi,
                    'total_jp' => $group->sum('jp_value'),
                    'course_count' => $group->count()
                ];
            })
            ->sortByDesc('total_jp')
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'jp_records' => $jpRecords,
                'monthly_jp' => $monthlyJp,
                'total_jp' => array_sum($monthlyJp),
                'jp_by_bidang_kompetensi' => $jpByBidangKompetensi
            ]
        ]);
    }

    /**
     * Get JP statistics.
     */
    public function getStatistics(): JsonResponse
    {
        $user = Auth::user();
        $currentYear = now()->year;
        
        // Get basic statistics
        $totalJpEarned = $user->jpRecords()->sum('jp_value');
        $jpThisYear = $user->jpRecords()
            ->whereYear('created_at', $currentYear)
            ->sum('jp_value');
        $totalCoursesCompleted = $user->jpRecords()->count();
        
        // Get JP by year
        $jpByYear = $user->jpRecords()
            ->selectRaw('YEAR(created_at) as year, SUM(jp_value) as total_jp, COUNT(*) as course_count')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        // Get JP by bidang kompetensi
        $jpByBidangKompetensi = $user->jpRecords()
            ->with('course:id,bidang_kompetensi')
            ->get()
            ->groupBy('course.bidang_kompetensi')
            ->map(function ($group) {
                return [
                    'bidang_kompetensi' => $group->first()->course->bidang_kompetensi,
                    'total_jp' => $group->sum('jp_value'),
                    'course_count' => $group->count()
                ];
            })
            ->sortByDesc('total_jp')
            ->values();

        // Get recent JP records
        $recentJpRecords = $user->jpRecords()
            ->with('course:id,judul,bidang_kompetensi')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate trends
        $currentYearJp = $jpByYear->where('year', $currentYear)->first();
        $previousYearJp = $jpByYear->where('year', $currentYear - 1)->first();
        
        $jpChange = 0;
        if ($previousYearJp && $previousYearJp->total_jp > 0) {
            $jpChange = round((($currentYearJp->total_jp - $previousYearJp->total_jp) / $previousYearJp->total_jp) * 100, 2);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_jp_earned' => $totalJpEarned,
                    'jp_this_year' => $jpThisYear,
                    'total_courses_completed' => $totalCoursesCompleted,
                    'jp_change_percentage' => $jpChange
                ],
                'by_year' => $jpByYear,
                'by_bidang_kompetensi' => $jpByBidangKompetensi,
                'recent' => $recentJpRecords
            ]
        ]);
    }

    /**
     * Search JP records.
     */
    public function search(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $query = $request->get('query');
        
        $jpRecords = $user->jpRecords()
            ->whereHas('course', function ($q) use ($query) {
                $q->where('judul', 'like', "%{$query}%")
                  ->orWhere('deskripsi', 'like', "%{$query}%")
                  ->orWhere('bidang_kompetensi', 'like', "%{$query}%");
            })
            ->with(['course:id,judul,bidang_kompetensi'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $jpRecords
        ]);
    }

    /**
     * Export JP records to CSV.
     */
    public function export(Request $request): Response
    {
        $user = Auth::user();
        
        $query = $user->jpRecords()
            ->with(['course']);

        // Apply filters
        if ($request->has('year') && $request->year !== '') {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->has('course_id') && $request->course_id !== '') {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('bidang_kompetensi') && $request->bidang_kompetensi !== '') {
            $query->whereHas('course', function ($q) use ($request) {
                $q->where('bidang_kompetensi', $request->bidang_kompetensi);
            });
        }

        $jpRecords = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV content
        $csvContent = "No,Kursus,Bidang Kompetensi,JP,Tanggal\n";
        
        foreach ($jpRecords as $index => $jpRecord) {
            $csvContent .= sprintf(
                "%d,%s,%s,%d,%s\n",
                $index + 1,
                $jpRecord->course->judul,
                $jpRecord->course->bidang_kompetensi,
                $jpRecord->jp_value,
                $jpRecord->created_at->format('d/m/Y')
            );
        }

        // Return CSV download
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="jp_records_' . now()->format('Y-m-d') . '.csv"'
        ]);
    }

    /**
     * Get JP progress towards target.
     */
    public function getTargetProgress(): JsonResponse
    {
        $user = Auth::user();
        $currentYear = now()->year;
        
        // Get JP earned this year
        $jpThisYear = $user->jpRecords()
            ->whereYear('created_at', $currentYear)
            ->sum('jp_value');
        
        // Target JP (assuming 40 JP per year)
        $targetJp = 40;
        $targetAchievement = $targetJp > 0 ? round(($jpThisYear / $targetJp) * 100, 2) : 0;
        
        // Get remaining JP needed
        $remainingJp = max(0, $targetJp - $jpThisYear);
        
        // Get JP by month to show progress
        $monthlyProgress = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthJp = $user->jpRecords()
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->sum('jp_value');
            
            $monthlyProgress[] = [
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'jp_value' => $monthJp,
                'target' => round($targetJp / 12, 2)
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'current_year' => $currentYear,
                'jp_earned' => $jpThisYear,
                'target_jp' => $targetJp,
                'target_achievement' => $targetAchievement,
                'remaining_jp' => $remainingJp,
                'monthly_progress' => $monthlyProgress
            ]
        ]);
    }

    /**
     * Get JP comparison between years.
     */
    public function getYearComparison(Request $request): JsonResponse
    {
        $user = Auth::user();
        $currentYear = now()->year;
        $compareYear = $request->get('compare_year', $currentYear - 1);
        
        // Get JP for both years
        $currentYearJp = $user->jpRecords()
            ->whereYear('created_at', $currentYear)
            ->sum('jp_value');
        
        $compareYearJp = $user->jpRecords()
            ->whereYear('created_at', $compareYear)
            ->sum('jp_value');
        
        // Calculate change
        $jpChange = 0;
        $jpChangePercentage = 0;
        
        if ($compareYearJp > 0) {
            $jpChange = $currentYearJp - $compareYearJp;
            $jpChangePercentage = round(($jpChange / $compareYearJp) * 100, 2);
        }
        
        // Get monthly comparison
        $monthlyComparison = [];
        for ($month = 1; $month <= 12; $month++) {
            $currentMonthJp = $user->jpRecords()
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->sum('jp_value');
            
            $compareMonthJp = $user->jpRecords()
                ->whereYear('created_at', $compareYear)
                ->whereMonth('created_at', $month)
                ->sum('jp_value');
            
            $monthlyComparison[] = [
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'current_year' => $currentMonthJp,
                'compare_year' => $compareMonthJp,
                'difference' => $currentMonthJp - $compareMonthJp
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'current_year' => [
                    'year' => $currentYear,
                    'total_jp' => $currentYearJp
                ],
                'compare_year' => [
                    'year' => $compareYear,
                    'total_jp' => $compareYearJp
                ],
                'comparison' => [
                    'jp_change' => $jpChange,
                    'jp_change_percentage' => $jpChangePercentage
                ],
                'monthly_comparison' => $monthlyComparison
            ]
        ]);
    }
} 