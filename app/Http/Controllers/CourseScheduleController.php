<?php

namespace App\Http\Controllers;

use App\Events\CourseScheduleUpdated;
use App\Http\Requests\UpdateCourseScheduleRequest;
use App\Models\Course;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CourseScheduleController extends Controller
{
    /**
     * Update the course schedule.
     */
    public function update(UpdateCourseScheduleRequest $request, $id): JsonResponse
    {
        // Trigger validation (rules live in the FormRequest); we ignore the returned array
        // and read directly from the request to avoid any casting/merge side-effects.
        $request->validated();

        $course = Course::findOrFail($id);
        $this->authorize('updateSchedule', $course);

        $oldStart = $course->start_date_time?->toIso8601String();
        $oldEnd = $course->end_date_time?->toIso8601String();

        // Read raw local datetimes from the request (HTML datetime-local format)
        $rawStart = $request->input('start_date_time');
        $rawEnd = $request->input('end_date_time');

        // Normalize empty strings to null (best practice for optional datetime fields)
        $rawStart = $rawStart === '' ? null : $rawStart;
        $rawEnd = $rawEnd === '' ? null : $rawEnd;

        // Convert from local timezone to immutable UTC instances before saving
        $course->start_date_time = $rawStart
            ? CarbonImmutable::parse($rawStart, config('app.timezone'))->setTimezone('UTC')
            : null;
        $course->end_date_time = $rawEnd
            ? CarbonImmutable::parse($rawEnd, config('app.timezone'))->setTimezone('UTC')
            : null;
        $course->updated_by = $request->user()->id;
        $course->save();

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
