<?php

namespace App\Http\Controllers;

use App\Events\CourseScheduleUpdated;
use App\Http\Requests\UpdateCourseScheduleRequest;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CourseScheduleController extends Controller
{
    /**
     * Update the course schedule.
     */
    public function update(UpdateCourseScheduleRequest $request, $id): JsonResponse
    {
        $course = Course::findOrFail($id);
        $this->authorize('updateSchedule', $course);

        $oldStart = $course->start_date_time?->toIso8601String();
        $oldEnd = $course->end_date_time?->toIso8601String();

        $course->update([
            'start_date_time' => $request->input('start_date_time'),
            'end_date_time' => $request->input('end_date_time'),
            'updated_by' => $request->user()->id,
        ]);

        // Log schedule change
        Log::info('Course schedule updated', [
            'course_id' => $course->id,
            'admin_id' => $request->user()->id,
            'old_start' => $oldStart,
            'new_start' => $course->start_date_time?->toIso8601String(),
            'old_end' => $oldEnd,
            'new_end' => $course->end_date_time?->toIso8601String(),
        ]);

        // Broadcast the update
        event(new CourseScheduleUpdated($course, $oldStart, $oldEnd));

        $response = [
            'success' => true,
            'message' => __('Schedule updated successfully.'),
            'course' => [
                'id' => $course->id,
                'start_date_time' => $course->start_date_time?->toIso8601String(),
                'end_date_time' => $course->end_date_time?->toIso8601String(),
                'schedule_status' => $course->scheduleStatus(),
            ],
        ];

        // Add warning if end is in the past
        if ($course->end_date_time && \Carbon\CarbonImmutable::parse($course->end_date_time, 'UTC')->isPast()) {
            $response['meta'] = [
                'warning' => __('The end date is in the past. Enrollment is now closed.'),
            ];
        }

        return response()->json($response);
    }
}
