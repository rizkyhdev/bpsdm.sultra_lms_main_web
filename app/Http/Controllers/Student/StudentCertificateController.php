<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentCertificateController extends Controller
{
    /**
     * Membuat instance controller baru.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Menampilkan daftar semua sertifikat yang diperoleh.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Only completed enrollments (completion_percent == 100 OR status == 'completed') and completed_at not null
        $enrollments = UserEnrollment::query()
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('completion_percent', 100)
                  ->orWhere('status', 'completed');
            })
            ->whereNotNull('completed_at')
            ->with([
                'course' => function ($q) {
                    $q->select('id', 'judul', 'slug', 'jp_value', 'bidang_kompetensi', 'user_id');
                },
                'course.owner:id,name',
            ])
            ->orderByDesc('completed_at')
            ->paginate(10);

        // Ensure each related course has a slug for certificate routes
        $enrollments->getCollection()->transform(function (UserEnrollment $enrollment) {
            $course = $enrollment->course;
            if ($course && empty($course->slug)) {
                $course->slug = \Illuminate\Support\Str::slug($course->judul);
                $course->save();
            }
            return $enrollment;
        });

        return view('student.certificates.index', [
            'enrollments' => $enrollments,
        ]);
    }
} 