<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login_page.login');
});
Route::get('/register', function () {
    return view('register_page.register');
});
Route::get('/modul', function () {
    return view('users.modul');
});
