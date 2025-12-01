<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentCalendarEventResource;
use App\Models\Course;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StudentCalendarController extends Controller
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
     * Get calendar events for the authenticated student within a date range.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $user = Auth::user();
        $from = CarbonImmutable::parse($request->input('from'), 'UTC');
        $to = CarbonImmutable::parse($request->input('to'), 'UTC');

        // Cache key includes user ID and date range (rounded to day)
        $cacheKey = "student.calendar.{$user->id}." . $from->format('Y-m-d') . '.' . $to->format('Y-m-d');
        $cacheTtl = 120; // 2 minutes

        $events = Cache::remember($cacheKey, $cacheTtl, function () use ($user, $from, $to) {
            return $this->fetchEvents($user, $from, $to);
        });

        return response()->json([
            'events' => StudentCalendarEventResource::collection($events),
            'from' => $from->toIso8601String(),
            'to' => $to->toIso8601String(),
        ]);
    }

    /**
     * Fetch calendar events for a user within a date range.
     *
     * @param \App\Models\User $user
     * @param CarbonImmutable $from
     * @param CarbonImmutable $to
     * @return \Illuminate\Support\Collection
     */
    protected function fetchEvents($user, CarbonImmutable $from, CarbonImmutable $to)
    {
        // Get all courses that have at least one date in the range
        // and that the student can see (enrolled or visible)
        try {
            $enrolledCourseIds = $user->userEnrollments()
                ->pluck('course_id')
                ->toArray();
        } catch (\Exception $e) {
            // Fallback if relationship fails
            $enrolledCourseIds = [];
        }

        // For now, all courses are visible to students (as per existing pattern)
        // In the future, you might add a visibility flag or policy
        $courses = Course::where(function ($query) use ($from, $to, $enrolledCourseIds) {
            $query->where(function ($q) use ($from, $to) {
                // Courses with start_date_time in range
                $q->whereNotNull('start_date_time')
                    ->whereBetween('start_date_time', [$from, $to]);
            })
            ->orWhere(function ($q) use ($from, $to) {
                // Courses with end_date_time in range
                $q->whereNotNull('end_date_time')
                    ->whereBetween('end_date_time', [$from, $to]);
            })
            ->orWhere(function ($q) use ($from, $to) {
                // Courses that span the range (start before, end after)
                $q->whereNotNull('start_date_time')
                    ->whereNotNull('end_date_time')
                    ->where('start_date_time', '<=', $to)
                    ->where('end_date_time', '>=', $from);
            });
        })
        ->get();

        $events = collect();

        foreach ($courses as $course) {
            $courseEvents = $this->mapCourseToEvents($course, $from, $to);
            $events = $events->merge($courseEvents);
        }

        return $events->sortBy('start_utc')->values();
    }

    /**
     * Map a course to calendar events based on its schedule.
     *
     * @param Course $course
     * @param CarbonImmutable $from
     * @param CarbonImmutable $to
     * @return \Illuminate\Support\Collection
     */
    protected function mapCourseToEvents(Course $course, CarbonImmutable $from, CarbonImmutable $to)
    {
        $events = collect();
        $start = $course->start_date_time ? CarbonImmutable::parse($course->start_date_time, 'UTC') : null;
        $end = $course->end_date_time ? CarbonImmutable::parse($course->end_date_time, 'UTC') : null;

        // Skip courses with neither start nor end
        if (!$start && !$end) {
            return $events;
        }

        // Validate: if both provided, start must be before end
        if ($start && $end && $start->gte($end)) {
            return $events; // Invalid schedule, skip
        }

        // Case 1: Both start and end - create a window event
        if ($start && $end) {
            // Only include if the window overlaps with the requested range
            if ($start->lte($to) && $end->gte($from)) {
                $events->push([
                    'id' => "course-{$course->id}-window",
                    'course_id' => $course->id,
                    'title' => $course->judul,
                    'start_utc' => $start->toIso8601String(),
                    'end_utc' => $end->toIso8601String(),
                    'type' => 'window',
                    'course_slug' => $course->slug,
                    'course_id_for_url' => $course->id,
                ]);
            }
        }
        // Case 2: Only start - create a start marker
        elseif ($start) {
            if ($start->gte($from) && $start->lte($to)) {
                $events->push([
                    'id' => "course-{$course->id}-start",
                    'course_id' => $course->id,
                    'title' => $course->judul,
                    'start_utc' => $start->toIso8601String(),
                    'end_utc' => null,
                    'type' => 'start',
                    'course_slug' => $course->slug,
                    'course_id_for_url' => $course->id,
                ]);
            }
        }
        // Case 3: Only end - create an end marker
        elseif ($end) {
            if ($end->gte($from) && $end->lte($to)) {
                $events->push([
                    'id' => "course-{$course->id}-end",
                    'course_id' => $course->id,
                    'title' => $course->judul,
                    'start_utc' => $end->toIso8601String(),
                    'end_utc' => null,
                    'type' => 'end',
                    'course_slug' => $course->slug,
                    'course_id_for_url' => $course->id,
                ]);
            }
        }

        return $events;
    }

    /**
     * Invalidate cache for a user's calendar.
     *
     * @param int $userId
     * @return void
     */
    public static function invalidateCache(int $userId): void
    {
        // Clear all calendar caches for this user
        // In production, you might want to use cache tags if supported
        Cache::flush(); // Simple approach - in production, use more targeted invalidation
    }
}

