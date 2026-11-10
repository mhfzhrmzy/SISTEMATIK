<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $isSuperadmin = Auth::guard('superadmin')->check();
        $isGuru = Auth::guard('admin')->check();
        $isSiswa = Auth::guard('siswa')->check();

        if ($role === 'superadmin') {
            if ($isSuperadmin) {
                return $next($request);
            }

            if ($isGuru || $isSiswa) {
                abort(403, 'Akses Ditolak: Halaman superadmin hanya boleh diakses oleh Superadmin.');
            }

            return redirect()->route('admin.login');
        }

        if ($role === 'guru') {
            if ($isGuru) {
                return $next($request);
            }

            if ($isSuperadmin || $isSiswa) {
                abort(403, 'Akses Ditolak: Halaman guru hanya boleh diakses oleh Guru.');
            }

            return redirect()->route('admin.login');
        }

        if ($role === 'siswa') {
            if ($isSiswa) {
                return $next($request);
            }

            if ($isSuperadmin || $isGuru) {
                abort(403, 'Akses Ditolak: Halaman siswa hanya boleh diakses oleh Siswa.');
            }

            return redirect()->route('siswa.login');
        }

        abort(403, 'Akses Ditolak: Role tidak valid.');
    }
}
