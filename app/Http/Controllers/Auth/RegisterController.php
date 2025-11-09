<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Tentukan redirect setelah registrasi berdasarkan role user.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = Auth::user();
        if (!$user) {
            return route('dashboard');
        }

        $role = $user->role ?? null;

        if ($role === 'admin') {
            return route('admin.dashboard');
        }

        if ($role === 'instructor') {
            return route('instructor.dashboard');
        }

        if ($role === 'supervisor') {
            return route('student.dashboard');
        }

        return route('student.dashboard');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nip' => ['required', 'string', 'max:255','unique:users'],
            'jabatan' => ['required', 'string', 'max:255'],
            'unit_kerja' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:student,instructor,admin'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Auto-validate users from BPSDM unit
        $isValidated = strtoupper(trim($data['unit_kerja'])) === 'BPSDMSULTRA';
        
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'nip' => $data['nip'],
             'jabatan' => $data['jabatan'],
              'unit_kerja' => $data['unit_kerja'],
            // Use selected role from registration form
            'role' => $data['role'] ?? 'student',
            // Auto-validate if unit_kerja is BPSDM, otherwise wait for admin approval
            'is_validated' => $isValidated,
        ]);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(\Illuminate\Http\Request $request, $user)
    {
        // If user is validated (e.g., from BPSDM), keep them logged in and redirect to dashboard
        if ($user->is_validated) {
            return redirect($this->redirectTo())
                ->with('success', 'Registrasi berhasil! Selamat datang di sistem LMS.');
        }

        // Logout user karena belum divalidasi
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke login dengan pesan sukses
        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu persetujuan dari admin. Anda akan dapat masuk ke sistem LMS setelah admin memvalidasi akun Anda.');
    }
}
