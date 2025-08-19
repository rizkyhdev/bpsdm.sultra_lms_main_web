<?php

namespace App\Http\Controllers\studentnurul;

use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        return view('wishlist'); // atau 'profile', sesuai file blade Anda
    }
}
