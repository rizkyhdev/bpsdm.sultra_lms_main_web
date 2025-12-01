<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\User;
use App\Models\UserEnrollment;
use App\Services\CertificateService;
use Illuminate\Console\Command;

class GenerateCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:generate {course_slug} {--user= : Specific user ID or email to generate for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate certificates for eligible users in a course';

    /**
     * Execute the console command.
     */
    public function handle(CertificateService $certificateService): int
    {
        $courseSlug = $this->argument('course_slug');
        $userOption = $this->option('user');

        $course = Course::where('slug', $courseSlug)->first();

        if (!$course) {
            $this->error("Course with slug '{$courseSlug}' not found.");
            return Command::FAILURE;
        }

        $this->info("Found course: {$course->judul}");

        // Get eligible enrollments (100% completion, completed_at not null)
        $query = UserEnrollment::where('course_id', $course->id)
            ->where('completion_percent', 100)
            ->whereNotNull('completed_at');

        if ($userOption) {
            // Try to find user by ID or email
            $user = User::where('id', $userOption)
                ->orWhere('email', $userOption)
                ->first();

            if (!$user) {
                $this->error("User '{$userOption}' not found.");
                return Command::FAILURE;
            }

            $query->where('user_id', $user->id);
            $this->info("Generating certificate for user: {$user->name} ({$user->email})");
        } else {
            $this->info('Generating certificates for all eligible users...');
        }

        $enrollments = $query->get();

        if ($enrollments->isEmpty()) {
            $this->warn('No eligible enrollments found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$enrollments->count()} eligible enrollment(s).");

        $bar = $this->output->createProgressBar($enrollments->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($enrollments as $enrollment) {
            try {
                $user = $enrollment->user;
                $certificateService->ensureCertificate($user, $course);
                $successCount++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed for user {$user->email}: {$e->getMessage()}");
                $errorCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Completed: {$successCount} success, {$errorCount} errors");

        return Command::SUCCESS;
    }
}
