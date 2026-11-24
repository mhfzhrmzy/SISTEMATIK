<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuruManajemenController extends Controller
{
    /**
     * Menampilkan daftar akun guru (digunakan dari halaman standalone /guru).
     */
    public function index()
    {
        $gurus = Guru::latest()->get();

        return view('guru.index', compact('gurus'));
    }

    /**
     * Menampilkan form edit akun guru.
     */
    public function edit(Guru $guru)
    {
        return view('guru.edit', ['admin' => $guru]);
    }

    /**
     * Menyimpan akun guru baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['nullable', 'digits:18', 'unique:guru,nip', 'required_without:nik'],
            'nik' => ['nullable', 'digits:16', 'unique:guru,nik', 'required_without:nip'],
            'nama_lengkap' => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z\s.,]+$/'],
            'password' => ['required', 'string', 'min:6'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'nip.digits' => 'NIP wajib tepat 18 digit angka.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nip.required_without' => 'NIP atau NIK wajib diisi.',
            'nik.digits' => 'NIK wajib tepat 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nik.required_without' => 'NIK atau NIP wajib diisi.',
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik (.), dan koma (,).',
            'foto_profile.max' => 'Ukuran foto profil tidak boleh lebih dari 5 MB.',
        ]);

        if ($request->hasFile('foto_profile')) {
            $validated['foto_profile'] = $request->file('foto_profile')->store('guru/foto', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['dibuat_oleh'] = Auth::guard('superadmin')->id();

        Guru::create($validated);

        $redirectRoute = $request->input('from') === 'superadmin' ? 'superadmin.index' : 'guru.index';
        $redirectParameters = $request->input('from') === 'superadmin' ? ['menu' => 'guru'] : [];

        return redirect()->route($redirectRoute, $redirectParameters)->with('success', 'Akun guru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data akun guru.
     */
    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nip' => ['nullable', 'digits:18', Rule::unique('guru', 'nip')->ignore($guru->id), 'required_without:nik'],
            'nik' => ['nullable', 'digits:16', Rule::unique('guru', 'nik')->ignore($guru->id), 'required_without:nip'],
            'nama_lengkap' => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z\s.,]+$/'],
            'password' => ['nullable', 'string', 'min:6'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'nip.digits' => 'NIP wajib tepat 18 digit angka.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nip.required_without' => 'NIP atau NIK wajib diisi.',
            'nik.digits' => 'NIK wajib tepat 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nik.required_without' => 'NIK atau NIP wajib diisi.',
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

        $redirectRoute = $request->input('from') === 'superadmin' ? 'superadmin.index' : 'guru.index';
        $redirectParameters = $request->input('from') === 'superadmin' ? ['menu' => 'guru'] : [];

        return redirect()->route($redirectRoute, $redirectParameters)->with('success', 'Akun guru berhasil diperbarui.');
    }

    /**
     * Menghapus akun guru beserta foto profilnya.
     */
    public function destroy(Guru $guru)
    {
        if ($guru->foto_profile) {
            Storage::disk('public')->delete($guru->foto_profile);
        }

        $guru->delete();

        $redirectRoute = request()->input('from') === 'superadmin' ? 'superadmin.index' : 'guru.index';
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
        $skippedCount = 0;

        while (($row = fgetcsv($handle, 2000, $delimiter)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $nipRaw = trim($row[0] ?? '');
            if (strtolower($nipRaw) === 'nip') {
                continue;
            }

            // Parse NIP
            $digitsOnly = preg_replace('/[^0-9]/', '', $nipRaw);
            $nip = strlen($digitsOnly) === 18 ? $digitsOnly : null;

            // Parse NIK
            $nikRaw = trim($row[1] ?? '');
            $nikDigitsOnly = preg_replace('/[^0-9]/', '', $nikRaw);
            $nik = strlen($nikDigitsOnly) === 16 ? $nikDigitsOnly : null;

            $namaLengkap = trim($row[2] ?? '');
            $passwordRaw = trim($row[3] ?? '');

            // Minimal salah satu (NIP atau NIK) harus ada dan nama lengkap harus ada
            if (($nip || $nik) && $namaLengkap !== '') {
                $password = $passwordRaw !== '' ? Hash::make($passwordRaw) : Hash::make('password123');

                // Cari berdasarkan NIP atau NIK
                $existing = null;
                if ($nip) {
                    $existing = Guru::where('nip', $nip)->first();
                }
                if (!$existing && $nik) {
                    $existing = Guru::where('nik', $nik)->first();
                }

                if ($existing) {
                    // Update existing
                    $existing->update([
                        'nip' => $nip,
                        'nik' => $nik,
                        'nama_lengkap' => $namaLengkap,
                        'password' => $password,
                        'dibuat_oleh' => Auth::guard('superadmin')->id(),
                    ]);
                } else {
                    // Create new
                    Guru::create([
                        'nip' => $nip,
                        'nik' => $nik,
                        'nama_lengkap' => $namaLengkap,
                        'password' => $password,
                        'dibuat_oleh' => Auth::guard('superadmin')->id(),
                    ]);
                }

                $importedCount++;
            } else {
                $skippedCount++;
            }
        }

        fclose($handle);

        $redirectRoute = $request->input('from') === 'superadmin' ? 'superadmin.index' : 'guru.index';
        $redirectParameters = $request->input('from') === 'superadmin' ? ['menu' => 'guru'] : [];

        $message = "Berhasil meng-import {$importedCount} data akun guru dari file CSV.";
        if ($skippedCount > 0) {
            $message .= " ({$skippedCount} baris dilewati karena data tidak valid)";
        }

        return redirect()->route($redirectRoute, $redirectParameters)->with('success', $message);
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
            fputcsv($handle, ['nip', 'nik', 'nama_lengkap', 'password']);
            fputcsv($handle, ['198501012010011001', '3509010185010001', 'Budi Santoso S.Pd', 'password123']);
            fputcsv($handle, ['', '3509031588030002', 'Siti Rahmawati M.Kom', 'password123']);
            fputcsv($handle, ['199005202015031003', '', 'Ahmad Fauzi S.T', 'password123']);
            fclose($handle);
        }, 200, $headers);
    }
}
