<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EnrollmentAndProgressSeeder extends Seeder
{
    /**
     * Enroll setiap student ke 2–4 kursus; buat progress submodul dengan timestamp koheren.
     */
    public function run()
    {
        $students = \App\Models\User::where('role', 'student')->get();
        $courseIds = \App\Models\Course::pluck('id')->all();

        foreach ($students as $student) {
            $numEnroll = rand(2, 4);
            $picked = (array) array_rand(array_flip($courseIds), $numEnroll);
            foreach ($picked as $courseId) {
                $enrollmentDate = Carbon::now()->subDays(rand(30, 240));
                // Distribusi status: enrolled 30%, in_progress 50%, completed 20%
                $r = rand(1, 100);
                $status = $r <= 30 ? 'enrolled' : ($r <= 80 ? 'in_progress' : 'completed');
                $completedAt = $status === 'completed' ? (clone $enrollmentDate)->addDays(rand(7, 90)) : null;

                $enrollment = \App\Models\UserEnrollment::factory()->create([
                    'user_id' => $student->id,
                    'course_id' => $courseId,
                    'enrollment_date' => $enrollmentDate,
                    'status' => $status,
                    'completed_at' => $completedAt,
                ]);

                // Progress submodul untuk course ini
                $subModules = \App\Models\SubModule::whereHas('module', function ($q) use ($courseId) {
                    $q->where('course_id', $courseId);
                })->get();

                foreach ($subModules as $sub) {
                    $completed = rand(0, 1) === 1 && $status !== 'enrolled';
                    \App\Models\UserProgress::factory()->create([
                        'user_id' => $student->id,
                        'sub_module_id' => $sub->id,
                        'is_completed' => $completed,
                        'completed_at' => $completed ? (clone $enrollmentDate)->addDays(rand(3, 60)) : null,
                        'created_at' => $enrollmentDate,
                        'updated_at' => $completed ? (clone $enrollmentDate)->addDays(rand(3, 60)) : $enrollmentDate,
                    ]);
                }
            }
        }
    }
}


