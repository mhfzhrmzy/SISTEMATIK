<?php

namespace App\Http\Controllers;

use App\Models\AdminGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminProfilController extends Controller
{
    public function show()
    {
        /** @var AdminGuru $admin */
        $admin = auth('admin')->user();
        return view('admin.profil', compact('admin'));
    }

    public function updateFoto(Request $request)
    {
        $request->validate(['foto' => 'required|image|mimes:jpg,jpeg,png|max:2048']);

        /** @var AdminGuru $admin */
        $admin = auth('admin')->user();

        if ($admin->foto_profile) {
            Storage::disk('public')->delete($admin->foto_profile);
        }

        $admin->foto_profile = $request->file('foto')->store('foto-guru', 'public');
        $admin->save();

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

        public function hapusFoto()
    {
        /** @var AdminGuru $admin */
        $admin = auth('admin')->user();

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

        /** @var AdminGuru $admin */
        $admin = auth('admin')->user();

        if (!Hash::check($request->password_lama, $admin->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $admin->password = $request->password_baru; // otomatis di-hash oleh cast
        $admin->save();

        return back()->with('success', 'Password berhasil diubah.');
    }
}