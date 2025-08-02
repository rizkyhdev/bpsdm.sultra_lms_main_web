<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnrolledController extends Controller
{
    public function index()
    {
        return view('enrolled'); 
    }
}
