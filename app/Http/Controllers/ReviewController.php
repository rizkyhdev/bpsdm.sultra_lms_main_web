<?php

// app/Http/Controllers/SettingController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return view('review');  // Mengembalikan view 'settings'
    }
}

