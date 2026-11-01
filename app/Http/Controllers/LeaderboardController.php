<?php

namespace App\Http\Controllers;

use App\Models\Leaderboard;
use App\Models\Materi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public const DAFTAR_KELAS = ['10 1', '10 2', '11 1', '11 2', '12 1', '12 2'];

    public const DAFTAR_JURUSAN = [
        'Teknik Alat Berat',
        'Teknik Kendaraan Ringan',
        'Teknik Sepeda Motor',
        'Teknik Pemesinan',
        'Teknik Instalasi Listrik',
        'Teknik Pembangkit Listrik',
        'Teknik Mekatronika',
        'Teknik Audio Video',
        'Teknik Komputer & Jaringan',
        'Teknik Konstruksi & Perumahan',
        'Desain Permodelan & Informasi Bangunan',
        'Desain Komunikasi Visual',
    ];

    public function index(Request $request): View
    {
        $isGuru = auth('admin')->check();
        $isSiswa = auth('siswa')->check();

        // 1. Filter Kelas, Jurusan, dan Tipe Test berdasarkan Aktor (Guru vs Siswa)
        if ($isSiswa) {
            $siswa = auth('siswa')->user();
            $kelas = $siswa->kelas;
            $jurusan = $siswa->jurusan;
            // Siswa HANYA menampilkan post-test di kelas & jurusannya sendiri
            $filterTest = 'posttest';

            // Materi yang dapat dipilih siswa hanya yang sesuai kelas & jurusannya
            $daftarMateri = Materi::with(['quiz' => function ($q) {
                $q->where('tipe_test', 'posttest');
            }])
                ->where('kelas', $kelas)
                ->where('jurusan', $jurusan)
                ->orderBy('judul_materi', 'asc')
                ->get();
        } else {
            // Aktor Guru / Tamu: Bebas memilih kelas, jurusan, dan tipe test
            $kelas = $request->query('kelas');
            $jurusan = $request->query('jurusan');

            if ($kelas && ! in_array($kelas, self::DAFTAR_KELAS)) {
                $kelas = null;
            }
            if (! $jurusan || ! in_array($jurusan, self::DAFTAR_JURUSAN)) {
                $jurusan = self::DAFTAR_JURUSAN[0];
            }

            $filterTest = $request->query('tipe_test');
            if (! in_array($filterTest, ['pretest', 'posttest'])) {
                $filterTest = null;
            }

            // Daftar Materi untuk Guru (disaring berdasarkan kelas dan jurusan jika dipilih)
            $materiQuery = Materi::with(['quiz']);
            if ($kelas) {
                $materiQuery->where(function ($q) use ($kelas) {
                    $q->where('kelas', $kelas)->orWhereNull('kelas');
                });
            }
            if ($jurusan) {
                $materiQuery->where(function ($q) use ($jurusan) {
                    $q->where('jurusan', $jurusan)->orWhereNull('jurusan');
                });
            }
            $daftarMateri = $materiQuery->orderBy('judul_materi', 'asc')->get();
        }

        // 2. Tipe Leaderboard: 'keseluruhan' atau 'permateri'
        $tipeLeaderboard = $request->query('tipe_leaderboard', 'keseluruhan');
        if (! in_array($tipeLeaderboard, ['keseluruhan', 'permateri'])) {
            $tipeLeaderboard = 'keseluruhan';
        }

        // 3. Materi Terpilih (jika mode permateri)
        $selectedMateriId = $request->query('materi_id');
        $selectedMateri = null;
        if ($selectedMateriId) {
            $selectedMateri = $daftarMateri->firstWhere('id_materi', $selectedMateriId);
            if (! $selectedMateri && ! $isSiswa) {
                $selectedMateri = Materi::with('quiz')->find($selectedMateriId);
            }
        }

        // Untuk Guru: jika mode permateri dan materi dipilih, null berarti "Semua (Pre & Post)" — biarkan apa adanya

        // 5. Query Leaderboard
        if ($tipeLeaderboard === 'permateri') {
            // Leaderboard per materi
            if ($selectedMateri) {
                if (! $filterTest) {
                    // "Semua (Pre & Post)": akumulasi poin pre-test + post-test per siswa untuk materi ini
                    $query = Leaderboard::with('pengguna')
                        ->whereHas('quiz', fn ($q) => $q->where('id_materi', $selectedMateri->id_materi));

                    if ($kelas || $jurusan) {
                        $query->whereHas('pengguna', function ($q) use ($kelas, $jurusan) {
                            if ($kelas) {
                                $q->where('kelas', $kelas);
                            }
                            if ($jurusan) {
                                $q->where('jurusan', $jurusan);
                            }
                        });
                    }

                    $leaderboards = $query
                        ->selectRaw('nisn, SUM(total_poin) as total_poin, COUNT(id_quiz) as total_kuis, MAX(updated_at) as updated_at')
                        ->groupBy('nisn')
                        ->orderByDesc('total_poin')
                        ->orderBy('updated_at')
                        ->get();
                } else {
                    // Tipe test spesifik (pretest / posttest): tampilkan baris per kuis
                    $query = Leaderboard::with(['quiz.materi', 'pengguna'])
                        ->whereHas('quiz', function ($q) use ($selectedMateri, $filterTest) {
                            $q->where('id_materi', $selectedMateri->id_materi);
                            $q->where('tipe_test', $filterTest);
                        });

                    if ($kelas || $jurusan) {
                        $query->whereHas('pengguna', function ($q) use ($kelas, $jurusan) {
                            if ($kelas) {
                                $q->where('kelas', $kelas);
                            }
                            if ($jurusan) {
                                $q->where('jurusan', $jurusan);
                            }
                        });
                    }

                    $leaderboards = $query->orderBy('total_poin', 'desc')
                        ->orderBy('updated_at', 'asc')
                        ->get();
                }
            } else {
                $leaderboards = collect();
            }
        } else {
            // Leaderboard keseluruhan: Akumulasi total skor per siswa untuk semua materi
            $query = Leaderboard::with('pengguna');

            if ($kelas || $jurusan) {
                $query->whereHas('pengguna', function ($q) use ($kelas, $jurusan) {
                    if ($kelas) {
                        $q->where('kelas', $kelas);
                    }
                    if ($jurusan) {
                        $q->where('jurusan', $jurusan);
                    }
                });
            }

            if ($filterTest) {
                $query->whereHas('quiz', fn ($q) => $q->where('tipe_test', $filterTest));
            }

            $leaderboards = $query->selectRaw('nisn, SUM(total_poin) as total_poin, COUNT(id_quiz) as total_kuis, MAX(updated_at) as updated_at')
                ->groupBy('nisn')
                ->orderByDesc('total_poin')
                ->orderBy('updated_at')
                ->get();
        }

        $daftarKelas = self::DAFTAR_KELAS;
        $daftarJurusan = self::DAFTAR_JURUSAN;

        return view('leaderboard.index', compact(
            'leaderboards',
            'filterTest',
            'kelas',
            'jurusan',
            'tipeLeaderboard',
            'daftarMateri',
            'selectedMateri',
            'selectedMateriId',
            'daftarKelas',
            'daftarJurusan',
            'isGuru',
            'isSiswa'
        ));
    }
}
