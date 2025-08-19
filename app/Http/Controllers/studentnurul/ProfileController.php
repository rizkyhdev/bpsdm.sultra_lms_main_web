<?php

namespace App\Http\Controllers\studentnurul;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile'); // atau 'profile', sesuai file blade Anda
    }
}
