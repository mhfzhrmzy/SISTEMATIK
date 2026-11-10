<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AdminGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuruManajemenController extends Controller
{
    /**
     * Menampilkan daftar akun guru (digunakan dari halaman standalone /admin).
     */
    public function index()
    {
        $gurus = AdminGuru::latest()->get();

        return view('admin.index', compact('gurus'));
    }

    /**
     * Menampilkan form edit akun guru.
     */
    public function edit(AdminGuru $guru)
    {
        return view('admin.edit', ['admin' => $guru]);
    }

    /**
     * Menyimpan akun guru baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['required', 'digits:18', 'unique:admin_guru,nip'],
            'nama_lengkap' => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z\s.,]+$/'],
            'password' => ['required', 'string', 'min:6'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'nip.digits' => 'NIP wajib tepat 18 digit angka.',
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik (.), dan koma (,).',
            'foto_profile.max' => 'Ukuran foto profil tidak boleh lebih dari 5 MB.',
        ]);

        if ($request->hasFile('foto_profile')) {
            $validated['foto_profile'] = $request->file('foto_profile')->store('guru/foto', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'guru';

        AdminGuru::create($validated);

        $redirectRoute = $request->input('from') === 'superadmin' ? 'superadmin.index' : 'admin.index';
        $redirectParameters = $request->input('from') === 'superadmin' ? ['menu' => 'guru'] : [];

        return redirect()->route($redirectRoute, $redirectParameters)->with('success', 'Akun guru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data akun guru.
     */
    public function update(Request $request, AdminGuru $guru)
    {
        $validated = $request->validate([
            'nip' => ['required', 'digits:18', Rule::unique('admin_guru', 'nip')->ignore($guru->nip, 'nip')],
            'nama_lengkap' => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z\s.,]+$/'],
            'password' => ['nullable', 'string', 'min:6'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'nip.digits' => 'NIP wajib tepat 18 digit angka.',
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik (.), dan koma (,).',
            'foto_profile.max' => 'Ukuran foto profil tidak boleh lebih dari 5 MB.',
        ]);

        if ($request->hasFile('foto_profile')) {
            if ($guru->foto_profile) {
                Storage::disk('public')->delete($guru->foto_profile);
            }
            $validated['foto_profile'] = $request->file('foto_profile')->store('guru/foto', 'public');
        } else {
            unset($validated['foto_profile']);
        }

        $validated['password'] = $request->filled('password')
            ? Hash::make($validated['password'])
            : $guru->password;

        $guru->update($validated);

        $redirectRoute = $request->input('from') === 'superadmin' ? 'superadmin.index' : 'admin.index';
        $redirectParameters = $request->input('from') === 'superadmin' ? ['menu' => 'guru'] : [];

        return redirect()->route($redirectRoute, $redirectParameters)->with('success', 'Akun guru berhasil diperbarui.');
    }

    /**
     * Menghapus akun guru beserta foto profilnya.
     */
    public function destroy(AdminGuru $guru)
    {
        if ($guru->foto_profile) {
            Storage::disk('public')->delete($guru->foto_profile);
        }

        $guru->delete();

        $redirectRoute = request()->input('from') === 'superadmin' ? 'superadmin.index' : 'admin.index';
        $redirectParameters = request()->input('from') === 'superadmin' ? ['menu' => 'guru'] : [];

        return redirect()->route($redirectRoute, $redirectParameters)->with('success', 'Akun guru berhasil dihapus.');
    }

    /**
     * Mengimpor data guru dari file CSV.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [
            'csv_file.required' => 'File CSV wajib diunggah.',
            'csv_file.mimes' => 'Format file harus berupa .csv',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return back()->with('error', 'Gagal membaca file CSV.');
        }

        $firstRow = fgetcsv($handle, 2000, ',');

        if ($firstRow && count($firstRow) === 1 && str_contains($firstRow[0], ';')) {
            rewind($handle);
            fgetcsv($handle, 2000, ';');
            $delimiter = ';';
        } else {
            $delimiter = ',';
        }

        $importedCount = 0;

        while (($row = fgetcsv($handle, 2000, $delimiter)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $nipRaw = trim($row[0] ?? '');
            if (strtolower($nipRaw) === 'nip') {
                continue;
            }

            $digitsOnly = preg_replace('/[^0-9]/', '', $nipRaw);
            $nip = str_pad($digitsOnly, 18, '0', STR_PAD_LEFT);
            $namaLengkap = trim($row[1] ?? '');
            $passwordRaw = trim($row[2] ?? '');

            if (strlen($nip) === 18 && $namaLengkap !== '') {
                $password = $passwordRaw !== '' ? Hash::make($passwordRaw) : Hash::make('password123');

                AdminGuru::updateOrCreate(
                    ['nip' => $nip],
                    ['nama_lengkap' => $namaLengkap, 'password' => $password]
                );

                $importedCount++;
            }
        }

        fclose($handle);

        $redirectRoute = $request->input('from') === 'superadmin' ? 'superadmin.index' : 'admin.index';
        $redirectParameters = $request->input('from') === 'superadmin' ? ['menu' => 'guru'] : [];

        return redirect()->route($redirectRoute, $redirectParameters)
            ->with('success', "Berhasil meng-import {$importedCount} data akun guru dari file CSV.");
    }

    /**
     * Mengunduh template CSV kosong untuk pengisian data guru.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_data_guru.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['nip', 'nama_lengkap', 'password']);
            fputcsv($handle, ['198501012010011001', 'Budi Santoso S.Pd', 'password123']);
            fputcsv($handle, ['198803152012022002', 'Siti Rahmawati M.Kom', 'password123']);
            fclose($handle);
        }, 200, $headers);
    }
}
