<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return view('student.profile.show', compact('user'));
    }

    /**
     * Edit the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('student.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            // 'phone' => 'nullable|string|max:255', // If you have these fields
            // 'timezone' => 'nullable|string|max:255',
            // 'locale' => 'nullable|string|max:255',
            // 'bio' => 'nullable|string',
            'surat_tugas_url' => 'nullable|url|max:255',
            'surat_tugas_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if ($request->has('surat_tugas_url')) {
            $user->surat_tugas_url = $validated['surat_tugas_url'];
        }

        if ($request->hasFile('surat_tugas_file')) {
            // Delete old file if exists
            if ($user->surat_tugas_file_path) {
                Storage::disk('public')->delete($user->surat_tugas_file_path);
            }
            
            $path = $request->file('surat_tugas_file')->store('surat_tugas', 'public');
            $user->surat_tugas_file_path = $path;
        }

        $user->save();

        return redirect()->route('student.profile.show')->with('status', 'Profil berhasil diperbarui.');
    }
}
