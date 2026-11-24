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
     * Guru bisa login dengan NIK atau NIP.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nip' => ['required', 'string', 'min:16', 'max:18'],
            'password' => ['required', 'string'],
        ], [
            'nip.required' => 'NIK/NIP wajib diisi.',
            'nip.min' => 'NIK/NIP minimal 16 digit.',
            'nip.max' => 'NIK/NIP maksimal 18 digit.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $identifier = $request->input('nip');
        $password = $request->input('password');

        // Coba login sebagai superadmin terlebih dahulu (hanya dengan NIP)
        if (strlen($identifier) === 18) {
            if (Auth::guard('superadmin')->attempt(['nip' => $identifier, 'password' => $password], $request->boolean('remember'))) {
                $request->session()->regenerate();
                $superadmin = Auth::guard('superadmin')->user();

                return redirect()->route('superadmin.index')
                    ->with('success', 'Selamat datang kembali, Superadmin '.$superadmin->nama_lengkap.'.');
            }
        }

        // Coba login sebagai guru dengan NIP (18 digit)
        if (strlen($identifier) === 18) {
            $guru = \App\Models\Guru::where('nip', $identifier)->first();
            if ($guru && Auth::guard('guru')->attempt(['id' => $guru->id, 'password' => $password], $request->boolean('remember'))) {
                $request->session()->regenerate();

                return redirect()->route('materi.index')
                    ->with('success', 'Selamat datang kembali, Guru '.$guru->nama_lengkap.'.');
            }
        }

        // Coba login sebagai guru dengan NIK (16 digit)
        if (strlen($identifier) === 16) {
            $guru = \App\Models\Guru::where('nik', $identifier)->first();
            if ($guru && Auth::guard('guru')->attempt(['id' => $guru->id, 'password' => $password], $request->boolean('remember'))) {
                $request->session()->regenerate();

                return redirect()->route('materi.index')
                    ->with('success', 'Selamat datang kembali, Guru '.$guru->nama_lengkap.'.');
            }
        }

        return back()
            ->withInput($request->only('nip'))
            ->withErrors(['nip' => 'NIK/NIP atau password salah.']);
    }

    /**
     * Melakukan logout untuk semua guard (superadmin & guru).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('superadmin')->logout();
        Auth::guard('guru')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('siswa.login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}
