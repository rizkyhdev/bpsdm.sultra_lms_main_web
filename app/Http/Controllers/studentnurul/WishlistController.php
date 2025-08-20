<?php

namespace App\Http\Controllers\studentnurul;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class WishlistController extends Controller
{
    public function index()
    {
        return view('student.wishlist'); // atau 'profile', sesuai file blade Anda
    }
}
