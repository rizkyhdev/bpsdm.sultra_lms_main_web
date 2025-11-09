<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Tentukan redirect setelah login berdasarkan role user.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = Auth::user();
        if (!$user) {
            return route('dashboard');
        }

        $role = $user->role ?? null; // sesuaikan jika menggunakan package role

        if ($role === 'admin') {
            return route('admin.dashboard');
        }

        if ($role === 'instructor') {
            return route('instructor.dashboard');
        }

        // default: student
        return route('student.dashboard');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        // Check if user is validated
        if (!$user->is_validated) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum divalidasi oleh admin. Silakan tunggu persetujuan admin untuk dapat masuk ke sistem LMS.');
        }

        return redirect()->intended($this->redirectPath());
    }
}
