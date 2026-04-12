<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PelatihanController extends Controller
{
    public function index()
    {
        $courses = \App\Models\Course::whereNotNull('start_date_time')
            ->orderBy('start_date_time', 'asc')
            ->get();

        $pelatihan = $courses->map(function ($course) {
            return [
                'title' => $course->judul,
                'description' => \Illuminate\Support\Str::limit($course->deskripsi, 100),
                'date' => \Carbon\Carbon::parse($course->start_date_time)->toDateString(),
                'start_time' => \Carbon\Carbon::parse($course->start_date_time)->format('H:i'),
                'end_time' => $course->end_date_time ? \Carbon\Carbon::parse($course->end_date_time)->format('H:i') : null,
                'end_date' => $course->end_date_time ? \Carbon\Carbon::parse($course->end_date_time)->toDateString() : null,
                'duration' => $course->jp_value . ' JP',
                'level' => 'Umum', // Default level or logic if available
                'url' => route('courses.show', $course->id)
            ];
        })->toArray();

        return view('welcome', compact('pelatihan'));
    }
}
