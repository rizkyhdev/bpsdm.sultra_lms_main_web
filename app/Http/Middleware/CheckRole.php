<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Menangani permintaan yang masuk.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Periksa apakah user memiliki role yang diperlukan
        if ($user->role !== $role) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Restrict access for unverified students
        if ($role === 'student' && !$user->is_validated) {
            $allowedRoutes = ['student.profile.show', 'student.profile.edit', 'student.profile.update', 'logout'];
            
            if (!in_array($request->route()?->getName(), $allowedRoutes)) {
                return redirect()->route('student.profile.edit')
                    ->with('warning', 'Akun Anda belum diverifikasi oleh Admin. Silakan lengkapi Surat Tugas atau Surat Bukti ASN Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }
} 