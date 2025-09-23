<?php

use Illuminate\Database\Seeder;
use App\Models\UserEnrollment;
use App\Models\Certificate;
use App\Models\JpRecord;

class CertificatesAndJpRecordsSeeder extends Seeder
{
    /**
     * Buat sertifikat & JP untuk enrollment yang completed.
     */
    public function run()
    {
        $completed = UserEnrollment::where('status', 'completed')->get();

        foreach ($completed as $enroll) {
            $issueDate = $enroll->completed_at ? clone $enroll->completed_at : \Carbon\Carbon::now()->subDays(rand(1, 30));
            $issueDate = (clone $issueDate)->addDays(rand(1, 14));

            // Sertifikat
            factory(Certificate::class)->create([
                'user_id' => $enroll->user_id,
                'course_id' => $enroll->course_id,
                'issue_date' => $issueDate,
                'nomor_sertifikat' => 'CERT-'.$issueDate->format('Y').'-'.str_pad((string) rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'file_path' => 'storage/app/public/certificates/CERT-'.$issueDate->format('Y').'-'.$enroll->user_id.'-'.$enroll->course_id.'.pdf',
            ]);

            // JP Record
            $course = $enroll->course; // relasi Eloquent
            factory(JpRecord::class)->create([
                'user_id' => $enroll->user_id,
                'course_id' => $enroll->course_id,
                'jp_earned' => $course ? $course->jp_value : rand(8, 40),
                'tahun' => (int) $issueDate->format('Y'),
                'recorded_at' => $issueDate,
            ]);
        }
    }
}


