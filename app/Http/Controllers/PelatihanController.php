<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PelatihanController extends Controller
{
    public function index()
    {
        $pelatihan = [
            [
                'title' => 'Pelatihan Sertifikasi PBJ',
                'description' => 'Pelatihan Sertifikasi PBJ untuk pemula',
                'date' => '2025-08-09',
                'duration' => '2 hours',
                'level' => 'Beginner',
                'url' => '#'
            ],
            [
                'title' => 'Pelatihan Lanjutan PBJ',
                'description' => 'Materi lanjutan PBJ',
                'date' => '2025-08-15',
                'duration' => '3 hours',
                'level' => 'Intermediate',
                'url' => '#'
            ],
            [
                'title' => 'Pelatihan Manajemen Bencana',
                'description' => 'Pelatihan Manajemen Bencana Lever Dasar',
                'date' => '2025-08-17',
                'duration' => '2 hours',
                'level' => 'Beginner',
                'url' => '#'
            ],
             [
                'title' => 'Pelatihan Sertifikasi PBJ',
                'description' => 'Pelatihan Sertifikasi PBJ untuk pemula',
                'date' => '2025-08-17',
                'duration' => '2 hours',
                'level' => 'Beginner',
                'url' => '#'
            ]
            
        ];

        return view('welcome', compact('pelatihan'));
    }
}
