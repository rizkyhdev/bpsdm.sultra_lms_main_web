<?php

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
    return view('landing');
})->name('landing');

// Route untuk Halaman Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Route untuk Halaman Course
Route::get('/course', function () {
    return view('course');
})->name('course');


// Route untuk Halaman Article
Route::get('/article', function () {
    return view('article');
})->name('article');

// Route untuk Halaman Contact
Route::get('/contact', function () {
    return view('contact');
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