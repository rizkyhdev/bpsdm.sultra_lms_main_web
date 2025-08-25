<?php

namespace App\Http\Controllers\studentnurul;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 

class EnrolledController extends Controller
{
    public function index()
    {
        return view('student.enrolled'); 
    }
}
