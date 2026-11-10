<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\PenggunaSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiswaManajemenController extends Controller
{
    /** Daftar kelas yang tersedia di SMK. */
    private const DAFTAR_KELAS = ['10 1', '10 2'];

    /** Daftar jurusan yang tersedia di SMK. */
    private const DAFTAR_JURUSAN = [
        'Teknik Alat Berat', 'Teknik Kendaraan Ringan', 'Teknik Sepeda Motor',
        'Teknik Pemesinan', 'Teknik Instalasi Listrik', 'Teknik Pembangkit Listrik',
        'Teknik Mekatronika', 'Teknik Audio Video', 'Teknik Komputer & Jaringan',
        'Teknik Konstruksi & Perumahan', 'Desain Permodelan & Informasi Bangunan',
        'Desain Komunikasi Visual',
    ];

    /**
     * Menampilkan daftar akun siswa (digunakan dari halaman standalone /siswa).
     */
    public function index()
    {
        $siswas = PenggunaSiswa::with('kelas.jurusan')->latest()->get();

        return view('siswa.index', compact('siswas'));
    }

    /**
     * Menampilkan form edit akun siswa.
     */
    public function edit(PenggunaSiswa $siswa)
    {
        $siswa->load('kelas.jurusan');

        return view('siswa.edit', compact('siswa'));
    }

    /**
     * Menyimpan akun siswa baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => ['required', 'digits:10', 'unique:pengguna_siswa,nisn'],
            'nama_lengkap' => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z\s]+$/'],
            'kelas' => ['required', 'in:'.implode(',', self::DAFTAR_KELAS)],
            'tahun' => ['required', 'digits:4'],
            'jurusan' => ['required', 'in:'.implode(',', self::DAFTAR_JURUSAN)],
            'password' => ['required', 'string', 'min:6'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'nisn.digits' => 'NISN wajib tepat 10 digit angka.',
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'tahun.required' => 'Tahun wajib diisi.',
            'jurusan.required' => 'Jurusan wajib dipilih.',
            'foto_profile.max' => 'Ukuran foto profil tidak boleh lebih dari 5 MB.',
        ]);

        $parts = explode(' ', $validated['kelas']);
        $tingkat = (int) ($parts[0] ?? 10);
        $rombel = (int) ($parts[1] ?? 1);

        $kelasModel = Kelas::findOrCreateByNama($validated['jurusan'], $tingkat, $rombel, $validated['tahun']);

        $fotoProfilePath = null;
        if ($request->hasFile('foto_profile')) {
            $fotoProfilePath = $request->file('foto_profile')->store('siswa/foto', 'public');
        }

        PenggunaSiswa::create([
            'nisn' => $validated['nisn'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'id_kelas' => $kelasModel->id_kelas,
            'password' => Hash::make($validated['password']),
            'foto_profile' => $fotoProfilePath,
            'dibuat_oleh' => Auth::guard('superadmin')->id(),
        ]);

        $redirectRoute = $request->input('from') === 'superadmin' ? 'superadmin.index' : 'siswa.index';
        $redirectParameters = $request->input('from') === 'superadmin' ? ['menu' => 'siswa'] : [];

        return redirect()->route($redirectRoute, $redirectParameters)->with('success', 'Akun siswa berhasil ditambahkan.');
    }

    /**
     * Memperbarui data akun siswa.
     */
    public function update(Request $request, PenggunaSiswa $siswa)
    {
        $validated = $request->validate([
            'nisn' => ['required', 'digits:10', Rule::unique('pengguna_siswa', 'nisn')->ignore($siswa->nisn, 'nisn')],
            'nama_lengkap' => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z\s]+$/'],
            'kelas' => ['required', 'in:'.implode(',', self::DAFTAR_KELAS)],
            'tahun' => ['required', 'digits:4'],
            'jurusan' => ['required', 'in:'.implode(',', self::DAFTAR_JURUSAN)],
            'password' => ['nullable', 'string', 'min:6'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'nisn.digits' => 'NISN wajib tepat 10 digit angka.',
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'tahun.required' => 'Tahun wajib diisi.',
            'jurusan.required' => 'Jurusan wajib dipilih.',
            'foto_profile.max' => 'Ukuran foto profil tidak boleh lebih dari 5 MB.',
        ]);

        $parts = explode(' ', $validated['kelas']);
        $tingkat = (int) ($parts[0] ?? 10);
        $rombel = (int) ($parts[1] ?? 1);

        $kelasModel = Kelas::findOrCreateByNama($validated['jurusan'], $tingkat, $rombel, $validated['tahun']);

        $dataToUpdate = [
            'nisn' => $validated['nisn'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'id_kelas' => $kelasModel->id_kelas,
        ];

        if ($request->hasFile('foto_profile')) {
            if ($siswa->foto_profile) {
                Storage::disk('public')->delete($siswa->foto_profile);
            }
            $dataToUpdate['foto_profile'] = $request->file('foto_profile')->store('siswa/foto', 'public');
        }

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        $siswa->update($dataToUpdate);

        $redirectRoute = $request->input('from') === 'superadmin' ? 'superadmin.index' : 'siswa.index';
        $redirectParameters = $request->input('from') === 'superadmin' ? ['menu' => 'siswa'] : [];

        return redirect()->route($redirectRoute, $redirectParameters)->with('success', 'Akun siswa berhasil diperbarui.');
    }

    /**
     * Menghapus akun siswa beserta foto profilnya.
     */
    public function destroy(PenggunaSiswa $siswa)
    {
        if ($siswa->foto_profile) {
            Storage::disk('public')->delete($siswa->foto_profile);
        }

        $siswa->delete();

        $redirectRoute = request()->input('from') === 'superadmin' ? 'superadmin.index' : 'siswa.index';
        $redirectParameters = request()->input('from') === 'superadmin' ? ['menu' => 'siswa'] : [];

        return redirect()->route($redirectRoute, $redirectParameters)->with('success', 'Akun siswa berhasil dihapus.');
    }

    /**
     * Mengimpor data siswa dari file CSV.
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

            $nisnRaw = trim($row[0] ?? '');
            if (strtolower($nisnRaw) === 'nisn') {
                continue;
            }

            $digitsOnly = preg_replace('/[^0-9]/', '', $nisnRaw);
            $nisn = str_pad($digitsOnly, 10, '0', STR_PAD_LEFT);
            $namaLengkap = trim($row[1] ?? '');
            $kelas = trim($row[2] ?? '');
            $tahun = trim($row[3] ?? '');
            $jurusan = trim($row[4] ?? '');
            $passwordRaw = trim($row[5] ?? '');

            if (strlen($nisn) === 10 && $namaLengkap !== '') {
                if (! in_array($kelas, self::DAFTAR_KELAS, true)) {
                    $kelas = '10 1';
                }
                if (! preg_match('/^\d{4}$/', $tahun)) {
                    $tahun = date('Y');
                }
                if (! in_array($jurusan, self::DAFTAR_JURUSAN, true)) {
                    $jurusan = 'Teknik Komputer & Jaringan';
                }

                $parts = explode(' ', $kelas);
                $tingkat = (int) ($parts[0] ?? 10);
                $rombel = (int) ($parts[1] ?? 1);

                $kelasModel = Kelas::findOrCreateByNama($jurusan, $tingkat, $rombel, $tahun);

                $password = $passwordRaw !== '' ? Hash::make($passwordRaw) : Hash::make('password123');

                PenggunaSiswa::updateOrCreate(
                    ['nisn' => $nisn],
                    [
                        'nama_lengkap' => $namaLengkap,
                        'id_kelas' => $kelasModel->id_kelas,
                        'password' => $password,
                        'dibuat_oleh' => Auth::guard('superadmin')->id(),
                    ]
                );

                $importedCount++;
            }
        }

        fclose($handle);

        $redirectRoute = $request->input('from') === 'superadmin' ? 'superadmin.index' : 'siswa.index';
        $redirectParameters = $request->input('from') === 'superadmin' ? ['menu' => 'siswa'] : [];

        return redirect()->route($redirectRoute, $redirectParameters)
            ->with('success', "Berhasil meng-import {$importedCount} data akun siswa dari file CSV.");
    }

    /**
     * Mengunduh template CSV kosong untuk pengisian data siswa.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_data_siswa.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['nisn', 'nama_lengkap', 'kelas', 'tahun', 'jurusan', 'password']);
            fputcsv($handle, ['0051234567', 'Ahmad Subagyo', '10 1', '2026', 'Teknik Komputer & Jaringan', 'password123']);
            fputcsv($handle, ['0051234568', 'Budi Utomo', '10 2', '2026', 'Desain Komunikasi Visual', 'password123']);
            fclose($handle);
        }, 200, $headers);
    }
}
