<?php

namespace App\Http\Controllers\studentnurul;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ProfileController extends Controller
{
    public function index()
    {


        return view('student.profile'); // atau 'profile', sesuai file blade Anda
    }
}
