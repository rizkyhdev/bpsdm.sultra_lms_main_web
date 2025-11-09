<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentWishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        $wishlist = $user->wishlists()
            ->withCount('userEnrollments as enrollments_count')
            ->paginate(12);

        return view('student.keinginan.index', compact('wishlist'));
    }

    /**
     * Add a course to the wishlist.
     */
    public function store(Course $course)
    {
        $user = Auth::user();

        // Check if already in wishlist
        if (!$user->wishlists()->where('course_id', $course->id)->exists()) {
            $user->wishlists()->attach($course->id);
        }

        return back()->with('success', 'Pelatihan ditambahkan ke daftar keinginan.');
    }

    /**
     * Remove a course from the wishlist.
     */
    public function destroy(Course $course)
    {
        $user = Auth::user();
        $user->wishlists()->detach($course->id);

        return back()->with('success', 'Pelatihan dihapus dari daftar keinginan.');
    }
}

