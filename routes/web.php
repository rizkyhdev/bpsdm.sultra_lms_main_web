<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\RegisterController;
Route::get('/', [PelatihanController::class, 'index']);
Route::get('/register', [RegisterController::class, 'showForm'])->name('register.show');
Route::post('/register', [RegisterController::class, 'submitForm'])->name('register.submit');

Auth::routes();

// Route::get('/login', function () {
//     return view('login_register_page.login');
// });
// Route::get('/register', function () {
//     return view('login_register_page.register');
// });
// Route::get('/resetPassword', function () {
//     return view('login_register_page.resetPassword');
// });
Route::get('/modul', function () {
    return view('users.modul');
});
Route::get('/sub_modul', function () {
    return view('users.sub_modul');
});
