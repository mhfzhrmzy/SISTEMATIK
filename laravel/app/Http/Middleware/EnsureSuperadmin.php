<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('superadmin')->check()) {
            return $next($request);
        }

        if (Auth::guard('admin')->check() || Auth::guard('siswa')->check()) {
            abort(403, 'Akses Ditolak: Halaman superadmin hanya boleh diakses oleh Superadmin.');
        }

        return redirect()->route('admin.login');
    }
}
