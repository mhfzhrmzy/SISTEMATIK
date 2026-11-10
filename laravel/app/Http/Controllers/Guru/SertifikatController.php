<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PenggunaSiswa;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SertifikatController extends Controller
{
    /**
     * Menampilkan daftar seluruh sertifikat yang sudah diterbitkan.
     */
    public function index()
    {
        $sertifikat = Sertifikat::with(['siswa', 'materi', 'guru'])
            ->latest()
            ->get();

        return view('guru.sertifikat.index', compact('sertifikat'));
    }

    /**
     * Mengembalikan daftar siswa dalam format JSON untuk filter dinamis di frontend.
     * Query params: kelas, jurusan
     */
    public function getSiswaByFilter(Request $request)
    {
        $query = PenggunaSiswa::orderBy('nama_lengkap');

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        $siswa = $query->get(['nisn', 'nama_lengkap', 'kelas', 'jurusan']);

        return response()->json($siswa);
    }

    /**
     * Menerima POST dari index (daftar siswa terpilih), lalu menampilkan form upload.
     * Menyimpan daftar NISN ke session agar aman dari manipulasi URL.
     */
    public function create(Request $request)
    {
        $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'exists:pengguna_siswa,nisn'],
        ], [
            'student_ids.required' => 'Pilih minimal satu siswa terlebih dahulu.',
            'student_ids.min' => 'Pilih minimal satu siswa terlebih dahulu.',
        ]);

        $siswaTerpilih = PenggunaSiswa::whereIn('nisn', $request->student_ids)
            ->orderBy('nama_lengkap')
            ->get(['nisn', 'nama_lengkap', 'kelas', 'jurusan']);

        session(['sertifikat_target_ids' => $siswaTerpilih->pluck('nisn')->toArray()]);

        return view('guru.sertifikat.create', compact('siswaTerpilih'));
    }

    /**
     * Menyimpan sertifikat dan mendistribusikannya ke semua siswa yang dipilih.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_materi' => ['nullable', 'exists:materi,id_materi'],
            'judul_sertifikat' => ['required', 'string', 'max:255'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'file_sertifikat' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $targetNisns = session('sertifikat_target_ids', []);

        if (empty($targetNisns)) {
            return redirect()->route('sertifikat.index')
                ->with('error', 'Sesi pilihan siswa telah habis. Silakan pilih siswa kembali.');
        }

        $fileSertifikat = $request->file('file_sertifikat');
        $hashFilePertama = hash_file('sha256', $fileSertifikat->getRealPath());
        $nipGuru = Auth::guard('admin')->user()->nip ?? null;
        $tanggalTerbit = now();
        $namaPenerbit = $validated['penerbit'] ?? 'SMKN 2 Jember';

        $pathFilePertama = $fileSertifikat->store('sertifikat/guru', 'public');
        $ekstensi = $fileSertifikat->getClientOriginalExtension();

        $jumlahBerhasil = 0;

        foreach ($targetNisns as $urutan => $nisn) {
            if ($urutan === 0) {
                $pathFile = $pathFilePertama;
                $hashFile = $hashFilePertama;
            } else {
                $namaFileBaru = 'sertifikat/guru/'.Str::uuid().'.'.$ekstensi;
                Storage::disk('public')->copy($pathFilePertama, $namaFileBaru);
                $pathFile = $namaFileBaru;
                $hashFile = hash('sha256', $hashFilePertama.$nisn);
            }

            Sertifikat::create([
                'nisn' => $nisn,
                'nip' => $nipGuru,
                'id_materi' => null,
                'judul_sertifikat' => $validated['judul_sertifikat'],
                'penerbit' => $namaPenerbit,
                'tanggal_terbit' => $tanggalTerbit,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'file_sertifikat' => $pathFile,
                'file_hash' => $hashFile,
                'tipe_sertifikat' => 'materi',
            ]);

            $jumlahBerhasil++;
        }

        session()->forget('sertifikat_target_ids');

        return redirect()->route('sertifikat.index')
            ->with('success', "Sertifikat berhasil diterbitkan untuk {$jumlahBerhasil} siswa.");
    }

    /**
     * Menghapus sertifikat beserta file fisiknya dari storage.
     */
    public function destroy(Sertifikat $sertifikat)
    {
        if ($sertifikat->file_sertifikat && Storage::disk('public')->exists($sertifikat->file_sertifikat)) {
            Storage::disk('public')->delete($sertifikat->file_sertifikat);
        }

        $sertifikat->delete();

        return back()->with('success', 'Sertifikat berhasil dihapus.');
    }
}
