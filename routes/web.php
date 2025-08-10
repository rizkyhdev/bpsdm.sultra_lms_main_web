<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelatihanController;
Route::get('/', [PelatihanController::class, 'index']);


Route::get('/login', function () {
    return view('login_register_page.login');
});
Route::get('/register', function () {
    return view('login_register_page.register');
});
Route::get('/modul', function () {
    return view('users.modul');
});
Route::get('/sub_modul', function () {
    return view('users.sub_modul');
});
