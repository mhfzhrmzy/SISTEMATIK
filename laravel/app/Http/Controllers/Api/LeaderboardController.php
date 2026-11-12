<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $siswa = $request->user();
        $kelas = $siswa->kelas;
        $jurusan = $siswa->jurusan;
        $tahun = $siswa->tahun;

        $leaderboards = Leaderboard::with('pengguna')
            ->whereHas('pengguna', function ($query) use ($kelas, $jurusan) {
                $query->where('kelas', $kelas)
                    ->where('jurusan', $jurusan);
            })
            ->whereHas('quiz.materi', fn ($q) => $q->where('tahun', $tahun))
            ->selectRaw('nisn, SUM(total_poin) as total_poin, COUNT(id_quiz) as total_kuis, MAX(updated_at) as updated_at')
            ->groupBy('nisn')
            ->orderByDesc('total_poin')
            ->orderBy('updated_at')
            ->get();

        $data = $leaderboards->map(function (Leaderboard $lb, $index) use ($siswa) {
            return [
                'nisn' => $lb->nisn,
                'nama_lengkap' => $lb->pengguna->nama_lengkap ?? '',
                'total_poin' => $lb->total_poin,
                'peringkat' => $index + 1,
                'is_me' => $lb->nisn === $siswa->nisn,
            ];
        });

        return response()->json([
            'message' => 'Leaderboard berhasil diambil.',
            'kelas' => $kelas,
            'jurusan' => $jurusan,
            'total_siswa' => $data->count(),
            'data' => $data,
        ]);
    }
}
