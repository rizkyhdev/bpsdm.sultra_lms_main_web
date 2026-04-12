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
            $start = \Carbon\Carbon::parse($course->start_date_time);
            $end = $course->end_date_time ? \Carbon\Carbon::parse($course->end_date_time) : null;
            
            return [
                'title' => $course->judul,
                'description' => \Illuminate\Support\Str::limit($course->deskripsi, 100),
                'date' => $start->toDateString(),
                'start_time' => $start->format('H:i'),
                'end_time' => $end ? $end->format('H:i') : null,
                'end_date' => $end ? $end->toDateString() : null,
                'duration' => $course->jp_value . ' JP',
                'level' => 'Umum',
                'url' => route('courses.public_show', $course->slug)
            ];
        })->toArray();

        return view('welcome', compact('pelatihan'));
    }
}
