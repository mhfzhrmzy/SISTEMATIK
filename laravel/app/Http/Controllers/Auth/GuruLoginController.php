<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruLoginController extends Controller
{
    /**
     * Menampilkan halaman login untuk Guru dan Superadmin.
     */
    public function create(): View
    {
        return view('auth.guru-login');
    }

    /**
     * Memproses percobaan login Guru atau Superadmin.
     * Login superadmin diprioritaskan terlebih dahulu.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'nip' => ['required', 'digits:18'],
            'password' => ['required', 'string'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.digits' => 'NIP wajib 18 digit angka.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Coba login sebagai superadmin terlebih dahulu
        if (Auth::guard('superadmin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $superadmin = Auth::guard('superadmin')->user();

            return redirect()->route('superadmin.index')
                ->with('success', 'Selamat datang kembali, Superadmin '.$superadmin->nama_lengkap.'.');
        }

        // Coba login sebagai guru
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $guru = Auth::guard('admin')->user();

            return redirect()->route('materi.index')
                ->with('success', 'Selamat datang kembali, Guru '.$guru->nama_lengkap.'.');
        }

        return back()
            ->withInput($request->only('nip'))
            ->withErrors(['nip' => 'NIP atau password salah.']);
    }

    /**
     * Melakukan logout untuk semua guard (superadmin & guru).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('superadmin')->logout();
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('siswa.login')->with('success', 'Anda telah berhasil logout.');
    }
}
