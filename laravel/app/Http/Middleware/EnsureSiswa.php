<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiswa
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('siswa')->check()) {
            return $next($request);
        }

        if (Auth::guard('superadmin')->check() || Auth::guard('admin')->check()) {
            abort(403, 'Akses Ditolak: Halaman siswa hanya boleh diakses oleh Siswa.');
        }

        return redirect()->route('siswa.login');
    }
}
