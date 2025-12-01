<?php

namespace App\Http\Resources;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentCalendarEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isEnrolled = false;
        if ($user) {
            try {
                $isEnrolled = $user->userEnrollments()
                    ->where('course_id', $this->resource['course_id'])
                    ->exists();
            } catch (\Exception $e) {
                // Fallback if relationship fails
                $isEnrolled = false;
            }
        }

        return [
            'id' => $this->resource['id'],
            'course_id' => $this->resource['course_id'],
            'title' => $this->resource['title'],
            'start_utc' => $this->resource['start_utc'],
            'end_utc' => $this->resource['end_utc'],
            'type' => $this->resource['type'], // 'window' | 'start' | 'end'
            'is_enrolled' => $isEnrolled,
            'course_slug' => $this->resource['course_slug'] ?? null,
            'course_id_for_url' => $this->resource['course_id_for_url'] ?? $this->resource['course_id'],
        ];
    }
}

