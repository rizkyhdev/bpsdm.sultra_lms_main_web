<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the admin's profile form.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return view('admin.profile.show', compact('user'));
    }

    /**
     * Edit the admin's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the admin's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'locale' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048', // up to 2MB
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if ($request->has('phone')) $user->phone = $validated['phone'];
        if ($request->has('timezone')) $user->timezone = $validated['timezone'];
        if ($request->has('locale')) $user->locale = $validated['locale'];
        if ($request->has('bio')) $user->bio = $validated['bio'];

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('admin.profile.show')->with('status', 'Profil berhasil diperbarui.');
    }
}
