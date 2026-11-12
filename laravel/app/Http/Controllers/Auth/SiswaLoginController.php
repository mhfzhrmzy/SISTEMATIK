<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaLoginController extends Controller
{
    public function create(): View
    {
        return view('auth.siswa-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'nisn' => ['required', 'digits:10'],
            'password' => ['required', 'string'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN wajib 10 digit angka.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (! Auth::guard('siswa')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('nisn'))->withErrors(['nisn' => 'NISN atau password salah.']);
        }

        $request->session()->regenerate();
        $siswa = Auth::guard('siswa')->user();

        return redirect()->route('portal.materi.index')->with('success', 'Selamat datang kembali, '.$siswa->nama_lengkap.'.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('siswa')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('siswa.login')->with('success', 'Anda telah berhasil logout.');
    }
}
