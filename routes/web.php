<?php

use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\Traits\StudentControllerTrait;
use App\Http\Controllers\studentnurul\ProfileController;   
use App\Http\Controllers\studentnurul\ReviewController;
use App\Http\Controllers\studentnurul\EnrolledController; 
use App\Http\Controllers\studentnurul\EnrolleActiveController;
use App\Http\Controllers\studentnurul\EnrolleCompleteController;
use App\Http\Controllers\studentnurul\WishlistController;
use App\Http\Controllers\studentnurul\ReviewsController;   
use App\Http\Controllers\studentnurul\SettingController;  
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Instructor\InstructorCourseController;
use App\Http\Controllers\Instructor\InstructorModuleController;
use App\Http\Controllers\Instructor\InstructorSubModuleController;
use App\Http\Controllers\Instructor\InstructorContentController;
use App\Http\Controllers\Instructor\InstructorQuizController;
use App\Http\Controllers\Instructor\InstructorQuestionController;
use App\Http\Controllers\Instructor\InstructorEnrollmentController;
use App\Http\Controllers\Instructor\InstructorProgressController;
use App\Http\Controllers\Instructor\InstructorAttemptController;
use App\Http\Controllers\Instructor\InstructorReportController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [PelatihanController::class, 'index']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Route untuk Landing Page (Halaman Utama)
Route::get('/landing', function () {
    return view('student.landing');
})->name('landing');

// Route untuk Halaman Dashboard
Route::get('/dashboard', function () {
    return view('student.dashboard');
})->name('dashboard');


// Route untuk Halaman Course
Route::get('/course', function () {
    return view('student.course');
})->name('course');

Route::get('/enroled-course', function(){
    return view('student.pelatihan');
})->name('enroled-course');


// Route untuk Halaman Article
Route::get('/article', function () {
    return view('student.article');
})->name('article');

// Route untuk Halaman Contact
Route::get('/contact', function () {
    return view('student.contact');
})->name('contact');


// Route untuk Halaman Profile
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
// Route::get('/profile', [StudentControllerTrait::class, 'index'])->name('profile');

Route::get('/review', [ReviewController::class, 'index'])->name('review');
// Route untuk Halaman Setting
Route::get('/settings', [SettingController::class, 'index'])->name('settings');

Route::get('/enrolled', [EnrolledController::class, 'index'])->name('enrolled');

Route::get('/active', [EnrolleActiveController::class, 'index'])->name('active');

Route::get('/complete', [EnrolleCompleteController::class, 'index'])->name('complete');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');



// Route untuk Halaman Review
Route::get('/reviews', [ReviewsController::class, 'index'])->name('reviews');

Route::get('/sub_modul', function () {
    return view('student.modul.sub_modul');
});
//Route::get('/enrolled', [EnrolleCourseController::class, 'index'])->name('course');

// Route untuk Sign Out (Logout)
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/'); // Redirect ke halaman utama setelah logout
})->name('logout');


// Isnan
// Route::get('/', [PelatihanController::class, 'index']);
// Route::get('/register2', [RegisterController::class, 'showForm'])->name('register.show');
// Route::post('/register2', [RegisterController::class, 'submitForm'])->name('register.submit');

// Route::get('/login2', function () {
//     return view('login_register_page.login');
// });
// Route::get('/register2', function () {
//     return view('login_register_page.register');
// });
// Route::get('/modul2', function () {
//     return view('users.modul');
// });
// Route::get('/sub_modul2', function () {
//     return view('users.sub_modul');
// });

// Rute untuk Instruktur
Route::group([
    'prefix' => 'instructor',
    'as' => 'instructor.',
    'middleware' => ['auth', 'role:instructor'],
], function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses/{course}/overview', [InstructorDashboardController::class, 'courseOverview'])->name('courses.overview');

    Route::get('/courses', [InstructorCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [InstructorCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [InstructorCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{id}', [InstructorCourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{id}/edit', [InstructorCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{id}', [InstructorCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{id}', [InstructorCourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{id}/duplicate', [InstructorCourseController::class, 'duplicate'])->name('courses.duplicate');

    Route::get('/courses/{courseId}/modules', [InstructorModuleController::class, 'index'])->name('modules.index');
    Route::get('/courses/{courseId}/modules/create', [InstructorModuleController::class, 'create'])->name('modules.create');
    Route::post('/courses/{courseId}/modules', [InstructorModuleController::class, 'store'])->name('modules.store');
    Route::get('/modules/{id}', [InstructorModuleController::class, 'show'])->name('modules.show');
    Route::get('/modules/{id}/edit', [InstructorModuleController::class, 'edit'])->name('modules.edit');
    Route::put('/modules/{id}', [InstructorModuleController::class, 'update'])->name('modules.update');
    Route::delete('/modules/{id}', [InstructorModuleController::class, 'destroy'])->name('modules.destroy');
    Route::post('/modules/reorder', [InstructorModuleController::class, 'reorder'])->name('modules.reorder');

    Route::get('/modules/{moduleId}/sub-modules', [InstructorSubModuleController::class, 'index'])->name('submodules.index');
    Route::get('/modules/{moduleId}/sub-modules/create', [InstructorSubModuleController::class, 'create'])->name('submodules.create');
    Route::post('/modules/{moduleId}/sub-modules', [InstructorSubModuleController::class, 'store'])->name('submodules.store');
    Route::get('/sub-modules/{id}', [InstructorSubModuleController::class, 'show'])->name('submodules.show');
    Route::get('/sub-modules/{id}/edit', [InstructorSubModuleController::class, 'edit'])->name('submodules.edit');
    Route::put('/sub-modules/{id}', [InstructorSubModuleController::class, 'update'])->name('submodules.update');
    Route::delete('/sub-modules/{id}', [InstructorSubModuleController::class, 'destroy'])->name('submodules.destroy');
    Route::post('/sub-modules/reorder', [InstructorSubModuleController::class, 'reorder'])->name('submodules.reorder');

    Route::get('/sub-modules/{subModuleId}/contents', [InstructorContentController::class, 'index'])->name('contents.index');
    Route::get('/sub-modules/{subModuleId}/contents/create', [InstructorContentController::class, 'create'])->name('contents.create');
    Route::post('/sub-modules/{subModuleId}/contents', [InstructorContentController::class, 'store'])->name('contents.store');
    Route::get('/contents/{id}', [InstructorContentController::class, 'show'])->name('contents.show');
    Route::get('/contents/{id}/edit', [InstructorContentController::class, 'edit'])->name('contents.edit');
    Route::put('/contents/{id}', [InstructorContentController::class, 'update'])->name('contents.update');
    Route::delete('/contents/{id}', [InstructorContentController::class, 'destroy'])->name('contents.destroy');
    Route::get('/contents/{id}/download', [InstructorContentController::class, 'download'])->name('contents.download');
    Route::post('/contents/reorder', [InstructorContentController::class, 'reorder'])->name('contents.reorder');

    Route::get('/sub-modules/{subModuleId}/quizzes', [InstructorQuizController::class, 'index'])->name('quizzes.index');
    Route::get('/sub-modules/{subModuleId}/quizzes/create', [InstructorQuizController::class, 'create'])->name('quizzes.create');
    Route::post('/sub-modules/{subModuleId}/quizzes', [InstructorQuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{id}', [InstructorQuizController::class, 'show'])->name('quizzes.show');
    Route::get('/quizzes/{id}/edit', [InstructorQuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{id}', [InstructorQuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{id}', [InstructorQuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::get('/quizzes/{id}/results', [InstructorQuizController::class, 'results'])->name('quizzes.results');

    Route::get('/quizzes/{quizId}/questions', [InstructorQuestionController::class, 'index'])->name('questions.index');
    Route::get('/quizzes/{quizId}/questions/create', [InstructorQuestionController::class, 'create'])->name('questions.create');
    Route::post('/quizzes/{quizId}/questions', [InstructorQuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{id}', [InstructorQuestionController::class, 'show'])->name('questions.show');
    Route::get('/questions/{id}/edit', [InstructorQuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{id}', [InstructorQuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{id}', [InstructorQuestionController::class, 'destroy'])->name('questions.destroy');
    Route::post('/questions/reorder', [InstructorQuestionController::class, 'reorder'])->name('questions.reorder');

    Route::get('/enrollments', [InstructorEnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/enrollments/{id}', [InstructorEnrollmentController::class, 'show'])->name('enrollments.show');
    Route::put('/enrollments/{id}', [InstructorEnrollmentController::class, 'update'])->name('enrollments.update');

    Route::get('/courses/{courseId}/progress', [InstructorProgressController::class, 'index'])->name('progress.index');
    Route::get('/courses/{courseId}/progress/users/{userId}', [InstructorProgressController::class, 'showUser'])->name('progress.show_user');

    Route::get('/quizzes/{quizId}/attempts', [InstructorAttemptController::class, 'index'])->name('attempts.index');
    Route::get('/attempts/{attemptId}', [InstructorAttemptController::class, 'show'])->name('attempts.show');
    Route::post('/attempts/{attemptId}/grade-essay', [InstructorAttemptController::class, 'gradeEssay'])->name('attempts.grade_essay');

    Route::get('/courses/{courseId}/report', [InstructorReportController::class, 'courseReport'])->name('reports.course');
    Route::get('/quizzes/{quizId}/report', [InstructorReportController::class, 'quizReport'])->name('reports.quiz');
    Route::get('/reports/export/{type}/{scopeId}', [InstructorReportController::class, 'export'])->name('reports.export');
});