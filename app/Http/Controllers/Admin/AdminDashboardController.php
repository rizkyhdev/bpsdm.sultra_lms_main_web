<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\UserEnrollment;
use App\Models\Certificate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $usersCount = User::count();
        $coursesCount = Course::count();
        $enrollmentsCount = UserEnrollment::count();
        
        $completedCount = UserEnrollment::where('status', 'completed')->count();
        $completionRate = $enrollmentsCount > 0 ? round(($completedCount / $enrollmentsCount) * 100, 1) : 0;

        $metrics = [
            'users_total' => $usersCount,
            'courses_total' => $coursesCount,
            'enrollments_total' => $enrollmentsCount,
            'completion_rate' => $completionRate,
        ];

        $recentEnrollments = UserEnrollment::with(['user', 'course'])
            ->latest()
            ->limit(5)
            ->get();

        $recentCertificates = Certificate::with(['user'])
            ->latest()
            ->limit(5)
            ->get();

        // Chart Data (Last 6 Months)
        $labels = [];
        $enrollmentSeries = [];
        $completionSeries = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            
            $enrollmentSeries[] = UserEnrollment::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
                
            $completionSeries[] = UserEnrollment::where('status', 'completed')
                ->whereMonth('completed_at', $month->month)
                ->whereYear('completed_at', $month->year)
                ->count();
        }

        $charts = [
            'enrollments' => [
                'labels' => $labels,
                'series' => $enrollmentSeries,
            ],
            'completions' => [
                'labels' => $labels,
                'series' => $completionSeries,
            ],
        ];

        return view('admin.dashboard', compact('metrics', 'recentEnrollments', 'recentCertificates', 'charts'));
    }
}


