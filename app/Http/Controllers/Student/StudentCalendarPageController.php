<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StudentCalendarPageController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Display the student calendar page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('student.calendar.index');
    }
}

