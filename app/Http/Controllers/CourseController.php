<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of courses with filters, search, and sorting.
     */
    public function index(Request $request)
    {
        $query = Course::query();

        // Search in judul and deskripsi
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('judul', 'like', "%{$searchTerm}%")
                  ->orWhere('deskripsi', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by categories (bidang_kompetensi)
        if ($request->filled('categories') && is_array($request->categories)) {
            $query->whereIn('bidang_kompetensi', $request->categories);
        }

        // Calculate sub_modules count and other aggregates
        $query->select('courses.*')
            ->selectRaw('(SELECT COUNT(sub_modules.id) FROM modules 
                         INNER JOIN sub_modules ON modules.id = sub_modules.module_id 
                         WHERE modules.course_id = courses.id) as sub_modules_count')
            ->selectRaw('(SELECT COUNT(contents.id) FROM modules 
                         INNER JOIN sub_modules ON modules.id = sub_modules.module_id 
                         INNER JOIN contents ON sub_modules.id = contents.sub_module_id 
                         WHERE modules.course_id = courses.id) as contents_count')
            ->selectRaw('(SELECT COUNT(quizzes.id) FROM modules 
                         INNER JOIN sub_modules ON modules.id = sub_modules.module_id 
                         INNER JOIN quizzes ON sub_modules.id = quizzes.sub_module_id 
                         WHERE modules.course_id = courses.id) as quizzes_count')
            ->selectRaw('(SELECT COUNT(user_enrollments.id) FROM user_enrollments 
                         WHERE user_enrollments.course_id = courses.id) as enrollments_count')
            ->selectRaw('COALESCE((SELECT AVG(quiz_attempts.nilai) FROM modules 
                         INNER JOIN sub_modules ON modules.id = sub_modules.module_id 
                         INNER JOIN quizzes ON sub_modules.id = quizzes.sub_module_id 
                         INNER JOIN quiz_attempts ON quizzes.id = quiz_attempts.quiz_id 
                         WHERE modules.course_id = courses.id), 0) as rating_avg');

        // Filter by difficulty based on sub_modules_count
        if ($request->filled('difficulty') && is_array($request->difficulty)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->difficulty as $diff) {
                    switch ($diff) {
                        case 'Beginner':
                            $q->orWhereRaw('(SELECT COUNT(sub_modules.id) FROM modules 
                                           INNER JOIN sub_modules ON modules.id = sub_modules.module_id 
                                           WHERE modules.course_id = courses.id) <= 4');
                            break;
                        case 'Intermediate':
                            $q->orWhereRaw('(SELECT COUNT(sub_modules.id) FROM modules 
                                           INNER JOIN sub_modules ON modules.id = sub_modules.module_id 
                                           WHERE modules.course_id = courses.id) BETWEEN 5 AND 9');
                            break;
                        case 'Expert':
                            $q->orWhereRaw('(SELECT COUNT(sub_modules.id) FROM modules 
                                           INNER JOIN sub_modules ON modules.id = sub_modules.module_id 
                                           WHERE modules.course_id = courses.id) >= 10');
                            break;
                    }
                }
            });
        }

        // Filter by rating (stars)
        if ($request->filled('rating')) {
            $rating = (int) $request->rating;
            $query->whereRaw('ROUND(COALESCE((SELECT AVG(quiz_attempts.nilai) FROM modules 
                         INNER JOIN sub_modules ON modules.id = sub_modules.module_id 
                         INNER JOIN quizzes ON sub_modules.id = quizzes.sub_module_id 
                         INNER JOIN quiz_attempts ON quizzes.id = quiz_attempts.quiz_id 
                         WHERE modules.course_id = courses.id), 0)) = ?', [$rating]);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('courses.id', 'asc');
                break;
            case 'highest_rated':
                $query->orderByRaw('(SELECT AVG(quiz_attempts.nilai) FROM modules 
                                   INNER JOIN sub_modules ON modules.id = sub_modules.module_id 
                                   INNER JOIN quizzes ON sub_modules.id = quizzes.sub_module_id 
                                   INNER JOIN quiz_attempts ON quizzes.id = quiz_attempts.quiz_id 
                                   WHERE modules.course_id = courses.id) DESC');
                break;
            case 'most_popular':
                $query->orderByRaw('(SELECT COUNT(user_enrollments.id) FROM user_enrollments 
                                   WHERE user_enrollments.course_id = courses.id) DESC');
                break;
            case 'latest':
            default:
                $query->orderBy('courses.id', 'desc');
                break;
        }

        // Eager load relationships for display
        $query->with('owner');

        // Paginate with query string preservation
        $courses = $query->paginate(12)->withQueryString();

        // Get distinct categories for sidebar
        $categories = Course::distinct()
            ->whereNotNull('bidang_kompetensi')
            ->pluck('bidang_kompetensi')
            ->filter()
            ->sort()
            ->values();

        // Difficulty options
        $difficulties = ['Beginner', 'Intermediate', 'Expert'];

        // Rating options (1-5 stars)
        $ratings = [1, 2, 3, 4, 5];

        // Calculate difficulty and rating_stars for each course
        $courses->getCollection()->transform(function ($course) {
            // Determine difficulty from sub_modules_count
            $subModulesCount = $course->sub_modules_count ?? 0;
            if ($subModulesCount <= 4) {
                $course->difficulty = 'Beginner';
            } elseif ($subModulesCount >= 5 && $subModulesCount <= 9) {
                $course->difficulty = 'Intermediate';
            } else {
                $course->difficulty = 'Expert';
            }

            // Round rating to nearest integer (0-5 stars)
            $course->rating_stars = (int) round($course->rating_avg ?? 0);

            return $course;
        });

        return view('courses.index', compact('courses', 'categories', 'difficulties', 'ratings'));
    }
}

