<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\UpdateEnrollmentRequest;
use App\Models\Course;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Kelas InstructorEnrollmentController
 * Menampilkan dan mengelola enrollment untuk kursus milik instruktur.
 */
class InstructorEnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    /**
     * Daftar enrollment untuk kursus milik instruktur.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', UserEnrollment::class);

        $courseId = $request->get('course_id');
        $status = $request->get('status');
        $from = $request->get('from');
        $to = $request->get('to');
        $perPage = (int) $request->get('per_page', 15);

        $ownedCourseIds = Course::where('user_id', Auth::id())->pluck('id');

        $enrollments = UserEnrollment::with(['user', 'course'])
            ->whereIn('course_id', $ownedCourseIds)
            ->when($courseId, function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($from, function ($q) use ($from) {
                $q->whereDate('enrollment_date', '>=', $from);
            })
            ->when($to, function ($q) use ($to) {
                $q->whereDate('enrollment_date', '<=', $to);
            })
            ->latest('enrollment_date')
            ->paginate($perPage)
            ->appends($request->query());

        return view('instructor.enrollments.index', compact('enrollments'));
    }

    /**
     * Tampilkan detail enrollment yang hanya milik kursus instruktur.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $enrollment = UserEnrollment::with(['user', 'course.modules.subModules'])
            ->findOrFail($id);
        $this->authorize('view', $enrollment);
        return view('instructor.enrollments.show', compact('enrollment'));
    }

    /**
     * Perbarui status enrollment.
     * @param UpdateEnrollmentRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateEnrollmentRequest $request, $id)
    {
        $enrollment = UserEnrollment::with('course')->findOrFail($id);
        $this->authorize('update', $enrollment);

        $enrollment->status = $request->validated()['status'];
        $enrollment->save();

        return redirect()->back()->with('success', 'Enrollment status updated.');
    }
}


