<?php

namespace App\Http\Controllers\studentnurul;

use Illuminate\Http\Request;

class EnrolledController extends Controller
{
    public function index()
    {
        return view('enrolled'); 
    }
}
