<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Logika bersama penerbitan sertifikat (filter siswa berbasis Post-Test & integrasi id_materi)
 * untuk portal Guru dan Superadmin.
 */
trait ManagesSertifikatPenerbitan
{
    abstract protected function sertifikatViewPrefix(): string;

    abstract protected function sertifikatIndexRoute(): string;

    abstract protected function sertifikatSessionPrefix(): string;

    abstract protected function sertifikatStorageFolder(): string;

    public function index()
    {
        $sertifikat = Sertifikat::with(['siswa.kelas.jurusan', 'materi', 'kategori'])
            ->latest()
            ->get();

        $daftarMateri = Materi::with('kelas.jurusan')
            ->orderBy('judul_materi')
            ->get();

        return view($this->sertifikatViewPrefix().'.index', compact('sertifikat', 'daftarMateri'));
    }

    /**
     * Mengembalikan daftar siswa dalam format JSON beserta riwayat nilai post-test per materi.
     * Query params: kelas, jurusan, id_materi
     */
    public function getSiswaByFilter(Request $request)
    {
        $query = PenggunaSiswa::with([
            'kelas.jurusan',
            'leaderboard' => function ($q) {
                $q->whereHas('quiz', fn ($qq) => $qq->where('tipe_test', 'posttest'))
                    ->with('quiz.materi');
            },
        ])->orderBy('nama_lengkap');

        if ($request->filled('kelas')) {
            $parts = preg_split('/[\s\-]+/', (string) $request->kelas);
            $tingkat = \App\Models\Kelas::parseTingkat($parts[0] ?? 0);
            $rombel = (int) ($parts[1] ?? 0);
            $query->whereHas('kelas', function ($q) use ($tingkat, $rombel) {
                $q->where('tingkat', $tingkat);
                if ($rombel > 0) {
                    $q->where('rombel', $rombel);
                }
            });
        }

        if ($request->filled('jurusan')) {
            $query->whereHas('kelas.jurusan', function ($q) use ($request) {
                $q->where('nama_jurusan', $request->jurusan);
            });
        }

        if ($request->filled('id_materi')) {
            $materiId = (int) $request->id_materi;
            $query->whereHas('leaderboard.quiz', function ($q) use ($materiId) {
                $q->where('tipe_test', 'posttest')
                    ->where('id_materi', $materiId);
            });
        }

        $siswa = $query->get(['nisn', 'nama_lengkap', 'id_kelas'])->map(function ($s) {
            $posttests = $s->leaderboard
                ->filter(fn ($lb) => $lb->quiz && $lb->quiz->id_materi)
                ->groupBy(fn ($lb) => (string) $lb->quiz->id_materi)
                ->map(function ($group) {
                    $best = $group->sortByDesc('total_poin')->first();

                    return [
                        'id_materi' => (int) $best->quiz->id_materi,
                        'judul_materi' => (string) ($best->quiz->materi?->judul_materi ?? ''),
                        'total_poin' => (int) $best->total_poin,
                    ];
                });

            return [
                'nisn' => $s->nisn,
                'nama_lengkap' => $s->nama_lengkap,
                'kelas' => (string) $s->kelas,
                'jurusan' => (string) ($s->jurusan ?? ''),
                'posttests' => $posttests,
            ];
        });

        return response()->json($siswa);
    }

    /**
     * Menampilkan form upload sertifikat untuk siswa terpilih (dan materi opsional dari filter).
     */
    public function create(Request $request)
    {
        $sessionIdsKey = $this->sertifikatSessionPrefix().'_target_ids';
        $sessionMateriKey = $this->sertifikatSessionPrefix().'_selected_materi_id';

        if ($request->isMethod('post')) {
            $request->validate([
                'student_ids' => ['required', 'array', 'min:1'],
                'student_ids.*' => ['required', 'exists:pengguna_siswa,nisn'],
                'id_materi' => ['nullable', 'exists:materi,id_materi'],
            ], [
                'student_ids.required' => 'Pilih minimal satu siswa terlebih dahulu.',
                'student_ids.min' => 'Pilih minimal satu siswa terlebih dahulu.',
            ]);

            $targetIds = $request->student_ids;
            session([
                $sessionIdsKey => $targetIds,
                $sessionMateriKey => $request->input('id_materi') ?: null,
            ]);
        } else {
            $targetIds = session($sessionIdsKey, []);
            if (empty($targetIds)) {
                return redirect()->route($this->sertifikatIndexRoute())
                    ->with('error', 'Pilih siswa terlebih dahulu sebelum menerbitkan sertifikat.');
            }
        }

        $siswaTerpilih = PenggunaSiswa::with([
            'kelas.jurusan',
            'leaderboard' => function ($q) {
                $q->whereHas('quiz', fn ($qq) => $qq->where('tipe_test', 'posttest'))
                    ->with('quiz');
            },
        ])
            ->whereIn('nisn', $targetIds)
            ->orderBy('nama_lengkap')
            ->get();

        session([$sessionIdsKey => $siswaTerpilih->pluck('nisn')->toArray()]);

        $daftarMateri = Materi::with('kelas.jurusan')
            ->orderBy('judul_materi')
            ->get();

        $selectedMateriId = $request->input('id_materi', session($sessionMateriKey));

        return view($this->sertifikatViewPrefix().'.create', compact('siswaTerpilih', 'daftarMateri', 'selectedMateriId'));
    }

    /**
     * Menyimpan sertifikat beserta id_materi dan mendistribusikannya ke semua siswa terpilih.
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

        $sessionIdsKey = $this->sertifikatSessionPrefix().'_target_ids';
        $sessionMateriKey = $this->sertifikatSessionPrefix().'_selected_materi_id';
        $targetNisns = session($sessionIdsKey, []);

        if (empty($targetNisns)) {
            return redirect()->route($this->sertifikatIndexRoute())
                ->with('error', 'Sesi pilihan siswa telah habis. Silakan pilih siswa kembali.');
        }

        $folder = $this->sertifikatStorageFolder();
        $fileSertifikat = $request->file('file_sertifikat');
        $hashFilePertama = hash_file('sha256', $fileSertifikat->getRealPath());
        $tanggalTerbit = now();
        $namaPenerbit = $validated['penerbit'] ?? 'SMKN 2 Jember';
        $idMateri = ! empty($validated['id_materi']) ? (int) $validated['id_materi'] : null;

        $pathFilePertama = $fileSertifikat->store($folder, 'public');
        $ekstensi = $fileSertifikat->getClientOriginalExtension();

        $jumlahBerhasil = 0;

        foreach ($targetNisns as $urutan => $nisn) {
            if ($urutan === 0) {
                $pathFile = $pathFilePertama;
            } else {
                $namaFileBaru = $folder.'/'.Str::uuid().'.'.$ekstensi;
                Storage::disk('public')->copy($pathFilePertama, $namaFileBaru);
                $pathFile = $namaFileBaru;
            }

            // Sertakan NISN & UUID agar unggahan ulang berkas/template yang sama tidak menabrak unique file_hash.
            $hashFile = hash('sha256', $hashFilePertama.'|'.$nisn.'|'.Str::uuid());

            Sertifikat::create([
                'nisn' => $nisn,
                'id_materi' => $idMateri,
                'judul_sertifikat' => $validated['judul_sertifikat'],
                'penerbit' => $namaPenerbit,
                'tanggal_terbit' => $tanggalTerbit,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'file_sertifikat' => $pathFile,
                'file_hash' => $hashFile,
                'tipe_sertifikat' => $idMateri ? 'materi' : 'mandiri',
            ]);

            $jumlahBerhasil++;
        }

        session()->forget([$sessionIdsKey, $sessionMateriKey]);

        return redirect()->route($this->sertifikatIndexRoute())
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
