<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class GuruProfilController extends Controller
{
    public function show()
    {
        /** @var Guru $admin */
        $admin = auth('guru')->user();

        return view('guru.profil', compact('admin'));
    }

    public function updateFoto(Request $request)
    {
        $request->validate(['foto' => 'required|image|mimes:jpg,jpeg,png|max:2048']);

        /** @var Guru $admin */
        $admin = auth('guru')->user();

        if ($admin->foto_profile) {
            Storage::disk('public')->delete($admin->foto_profile);
        }

        $admin->foto_profile = $request->file('foto')->store('foto-guru', 'public');
        $admin->save();

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function hapusFoto()
    {
        /** @var Guru $admin */
        $admin = auth('guru')->user();

        if ($admin->foto_profile) {
            Storage::disk('public')->delete($admin->foto_profile);
        }

        $admin->foto_profile = null;
        $admin->save();

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8',
        ]);

        /** @var Guru $admin */
        $admin = auth('guru')->user();

        if (! Hash::check($request->password_lama, $admin->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $admin->password = $request->password_baru; // otomatis di-hash oleh cast
        $admin->save();

        return back()->with('success', 'Password berhasil diubah.');
    }
}
