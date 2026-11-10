<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminGuru;
use App\Models\PenggunaSiswa;
use App\Models\SuperAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function loginSiswa(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nisn' => ['required', 'digits:10'],
            'password' => ['required', 'string'],
        ]);

        $siswa = PenggunaSiswa::where('nisn', $validated['nisn'])->first();

        if (! $siswa || ! Hash::check($validated['password'], $siswa->password)) {
            return response()->json([
                'message' => 'NISN atau password salah.',
            ], 401);
        }

        $token = $siswa->createToken('siswa-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'nisn' => $siswa->nisn,
                'nama_lengkap' => $siswa->nama_lengkap,
                'kelas' => $siswa->kelas,
                'jurusan' => $siswa->jurusan,
            ],
        ]);
    }

    public function loginGuru(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'digits:18'],
            'password' => ['required', 'string'],
        ]);

        $superadmin = SuperAdmin::where('nip', $validated['nip'])->first();

        if ($superadmin && Hash::check($validated['password'], $superadmin->password)) {
            $token = $superadmin->createToken('superadmin-token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil.',
                'role' => 'superadmin',
                'token' => $token,
                'user' => [
                    'nip' => $superadmin->nip,
                    'nama_lengkap' => $superadmin->nama_lengkap,
                ],
            ]);
        }

        $guru = AdminGuru::where('nip', $validated['nip'])->first();

        if ($guru && Hash::check($validated['password'], $guru->password)) {
            $token = $guru->createToken('guru-token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil.',
                'role' => 'guru',
                'token' => $token,
                'user' => [
                    'nip' => $guru->nip,
                    'nama_lengkap' => $guru->nama_lengkap,
                ],
            ]);
        }

        return response()->json([
            'message' => 'NIP atau password salah.',
        ], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    public function profilSiswa(Request $request): JsonResponse
    {
        $siswa = $request->user();

        $fotoUrl = $siswa->foto_profile
            ? Storage::disk('public')->url($siswa->foto_profile)
            : null;

        return response()->json([
            'message' => 'Profil siswa berhasil diambil.',
            'user' => [
                'nisn' => $siswa->nisn,
                'nama_lengkap' => $siswa->nama_lengkap,
                'kelas' => $siswa->kelas,
                'jurusan' => $siswa->jurusan,
                'poin' => $siswa->poin,
                'foto_profile' => $fotoUrl,
            ],
        ]);
    }

    public function updateFotoSiswa(Request $request): JsonResponse
    {
        $request->validate([
            'foto' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $siswa = $request->user();

        if ($siswa->foto_profile && Storage::disk('public')->exists($siswa->foto_profile)) {
            Storage::disk('public')->delete($siswa->foto_profile);
        }

        $path = $request->file('foto')->store('foto_profile/siswa', 'public');
        $siswa->update(['foto_profile' => $path]);

        return response()->json([
            'message' => 'Foto profil berhasil diperbarui.',
            'foto_profile' => Storage::disk('public')->url($path),
        ]);
    }

    public function updatePasswordSiswa(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password_lama' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $siswa = $request->user();

        if (! Hash::check($validated['password_lama'], $siswa->password)) {
            return response()->json([
                'message' => 'Password lama tidak cocok.',
            ], 422);
        }

        $siswa->update(['password' => $validated['password_baru']]);

        return response()->json([
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
