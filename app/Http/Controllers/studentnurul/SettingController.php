<?php

// app/Http/Controllers/SettingController.php
namespace App\Http\Controllers\studentnurul;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class SettingController extends Controller
{
    public function index()
    {
        return view('settings');  // Mengembalikan view 'settings'
    }
}

