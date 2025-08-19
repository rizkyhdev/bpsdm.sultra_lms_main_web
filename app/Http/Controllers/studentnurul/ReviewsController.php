<?php

namespace App\Http\Controllers\studentnurul;

use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function index()
    {
        return view('reviews');  // Mengembalikan view 'settings'
    }
}
