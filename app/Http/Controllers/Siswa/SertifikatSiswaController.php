<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SertifikatSiswaController extends Controller
{
    private function currentNisn()
    {
        return Auth::guard('siswa')->user()->nisn ?? Auth::user()->nisn;
    }

        public function index(Request $request)
    {
        $nisn   = $this->currentNisn();
        $dari   = $this->parseTanggal($request->query('dari'));
        $sampai = $this->parseTanggal($request->query('sampai'));

        // Kalau tertukar, tukar balik
        if ($dari && $sampai && $dari->gt($sampai)) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        $sertifikat = Sertifikat::where('nisn', $nisn)
            ->when($dari, fn ($q) => $q->whereDate('tanggal_terbit', '>=', $dari->toDateString()))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal_terbit', '<=', $sampai->toDateString()))
            ->with(['materi', 'guru'])
            ->orderByDesc('tanggal_terbit')
            ->latest()
            ->get();

        $filterAktif = (bool) ($dari || $sampai);

        return view('siswa.sertifikat.index', compact('sertifikat', 'dari', 'sampai', 'filterAktif'));
    }

    private function parseTanggal(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function create()
    {
        return view('siswa.sertifikat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_sertifikat' => ['required', 'string', 'max:255'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            'tanggal_terbit' => ['nullable', 'date'],
            'deskripsi' => ['nullable', 'string'],
            'file_sertifikat' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $path = $request->file('file_sertifikat')->store('sertifikat/siswa', 'public');

        Sertifikat::create([
            'nisn' => $this->currentNisn(),
            'nip' => null,
            'id_materi' => null,
            'judul_sertifikat' => $validated['judul_sertifikat'],
            'penerbit' => $validated['penerbit'] ?? null,
            'tanggal_terbit' => $validated['tanggal_terbit'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'file_sertifikat' => $path,
            'tipe_sertifikat' => 'mandiri',
        ]);

        return redirect()
            ->route('siswa.sertifikat.index')
            ->with('success', 'Sertifikat berhasil diunggah.');
    }

    public function destroy(Sertifikat $sertifikat)
    {
        // Cegah penghapusan jika bukan milik siswa yang login
        abort_if($sertifikat->nisn != $this->currentNisn(), 403);

        // Hanya sertifikat unggahan mandiri yang boleh dihapus oleh siswa
        abort_if($sertifikat->tipe_sertifikat !== 'mandiri', 403, 'Sertifikat dari guru tidak dapat dihapus.');

        if ($sertifikat->file_sertifikat && Storage::disk('public')->exists($sertifikat->file_sertifikat)) {
            Storage::disk('public')->delete($sertifikat->file_sertifikat);
        }

        $sertifikat->delete();

        return back()->with('success', 'Sertifikat berhasil dihapus.');
    }
}
