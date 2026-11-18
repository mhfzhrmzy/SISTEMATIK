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
        $siswa->load('kelas.jurusan');

        $tipeLeaderboard = $request->query('tipe_leaderboard', 'keseluruhan');
        if (! in_array($tipeLeaderboard, ['keseluruhan', 'permateri'])) {
            $tipeLeaderboard = 'keseluruhan';
        }

        $materiId = $request->query('materi_id');
        if ($tipeLeaderboard === 'permateri' && ! $materiId) {
            return response()->json([
                'message' => 'Paramater materi_id wajib diisi untuk leaderboard permateri.',
            ], 422);
        }

        $leaderboards = Leaderboard::with('pengguna')
            ->whereHas('pengguna', function ($query) use ($siswa) {
                $query->where('id_kelas', $siswa->id_kelas);
            })
            ->whereHas('quiz.materi.kelas', function ($query) use ($siswa) {
                $query->where('tahun', $siswa->tahun);
            });

        if ($tipeLeaderboard === 'permateri') {
            $materiId = (int) $materiId;
            $leaderboards->whereHas(
                'quiz.materi',
                fn ($q) => $q->where('id_materi', $materiId),
            );
        }

        $leaderboards = $leaderboards
            ->selectRaw('nisn, SUM(total_poin) as total_poin, COUNT(id_quiz) as total_kuis, MAX(updated_at) as updated_at')
            ->groupBy('nisn')
            ->orderByDesc('total_poin')
            ->orderBy('updated_at')
            ->get();

        $data = $leaderboards->map(function (Leaderboard $lb, $index) use ($siswa) {
            return [
                'nisn' => $lb->nisn,
                'nama_lengkap' => $lb->pengguna->nama_lengkap ?? '',
                'total_poin' => (int) $lb->total_poin,
                'peringkat' => $index + 1,
                'is_me' => $lb->nisn === $siswa->nisn,
            ];
        });

        return response()->json([
            'message' => 'Leaderboard berhasil diambil.',
            'tipe_leaderboard' => $tipeLeaderboard,
            'materi_id' => $tipeLeaderboard === 'permateri' ? (int) $materiId : null,
            'kelas' => $siswa->kelas?->nama_kelas,
            'jurusan' => $siswa->jurusan,
            'total_siswa' => $data->count(),
            'data' => $data,
        ]);
    }
}
