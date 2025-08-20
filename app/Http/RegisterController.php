<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('login_register_page.register');
    }

    public function submitForm(Request $request)
    {
        // Data dummy saja (tanpa database)
        $nip = $request->input('nip');
        $username = $request->input('username');

        // Simulasikan delay proses registrasi
        sleep(1);

        // Redirect kembali ke form dengan flash session
        return redirect()->back()->with('success', "Akun demo untuk {$username} berhasil dibuat!");
    }
}
