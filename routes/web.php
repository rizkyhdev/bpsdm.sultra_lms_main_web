<?php

use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\studentnurul\ProfileController;   
use App\Http\Controllers\studentnurul\ReviewController;
use App\Http\Controllers\studentnurul\EnrolledController; 
use App\Http\Controllers\studentnurul\EnrolleActiveController;
use App\Http\Controllers\studentnurul\EnrolleCompleteController;
use App\Http\Controllers\studentnurul\WishlistController;
use App\Http\Controllers\studentnurul\ReviewsController;   
use App\Http\Controllers\studentnurul\SettingController;  
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

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

Route::get('/review', [ReviewController::class, 'index'])->name('review');
// Route untuk Halaman Setting
Route::get('/settings', [SettingController::class, 'index'])->name('settings');

Route::get('/enrolled', [EnrolledController::class, 'index'])->name('enrolled');

Route::get('/active', [EnrolleActiveController::class, 'index'])->name('active');

Route::get('/complete', [EnrolleCompleteController::class, 'index'])->name('complete');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');



// Route untuk Halaman Review
Route::get('/reviews', [ReviewsController::class, 'index'])->name('reviews');

//Route::get('/enrolled', [EnrolleCourseController::class, 'index'])->name('course');

// Route untuk Sign Out (Logout)
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/'); // Redirect ke halaman utama setelah logout
})->name('logout');


// Isnan
Route::get('/home2', [PelatihanController::class, 'index']);
Route::get('/register2', [RegisterController::class, 'showForm'])->name('register.show');
Route::post('/register2', [RegisterController::class, 'submitForm'])->name('register.submit');

Route::get('/login2', function () {
    return view('login_register_page.login');
});
Route::get('/register2', function () {
    return view('login_register_page.register');
});
Route::get('/modul2', function () {
    return view('users.modul');
});
Route::get('/sub_modul2', function () {
    return view('users.sub_modul');
});