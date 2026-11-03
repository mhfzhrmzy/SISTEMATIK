<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use App\Models\Materi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MateriController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $siswa = $request->user();

        $materis = Materi::with(['adminGuru', 'quiz'])
            ->where('kelas', $siswa->kelas)
            ->where('jurusan', $siswa->jurusan)
            ->where('tahun', $siswa->tahun)
            ->latest()
            ->get()
            ->map(function (Materi $materi) use ($siswa) {
                $quizzes = $materi->quiz;
                $pretests = $quizzes->where('tipe_test', 'pretest');
                $pretestCompleted = false;

                if ($pretests->isNotEmpty()) {
                    $pretestCompleted = Leaderboard::where('nisn', $siswa->nisn)
                        ->whereIn('id_quiz', $pretests->pluck('id_quiz'))
                        ->exists();
                } else {
                    $pretestCompleted = true;
                }

                return [
                    'id_materi' => $materi->id_materi,
                    'judul_materi' => $materi->judul_materi,
                    'isi_materi' => $materi->isi_materi,
                    'kelas' => $materi->kelas,
                    'jurusan' => $materi->jurusan,
                    'upload_file' => $materi->upload_file
                        ? Storage::disk('public')->url($materi->upload_file)
                        : null,
                    'guru_pengajar' => $materi->adminGuru
                        ? $materi->adminGuru->nama_lengkap
                        : null,
                    'quiz_count' => $quizzes->count(),
                    'pretest_count' => $pretests->count(),
                    'pretest_completed' => $pretestCompleted,
                ];
            });

        return response()->json([
            'message' => 'Daftar materi berhasil diambil.',
            'data' => $materis,
        ]);
    }

    public function show(Request $request, Materi $materi): JsonResponse
    {
        $siswa = $request->user();

        if ($materi->kelas !== $siswa->kelas || $materi->jurusan !== $siswa->jurusan || $materi->tahun !== $siswa->tahun) {
            return response()->json([
                'message' => 'Kamu tidak memiliki akses ke materi ini.',
            ], 403);
        }

        $materi->load(['adminGuru', 'quiz.soal']);

        $pretests = $materi->quiz->where('tipe_test', 'pretest');
        $pretestCompleted = false;

        if ($pretests->isNotEmpty()) {
            $pretestCompleted = Leaderboard::where('nisn', $siswa->nisn)
                ->whereIn('id_quiz', $pretests->pluck('id_quiz'))
                ->exists();
        } else {
            $pretestCompleted = true;
        }

        $quizzes = $materi->quiz->map(function ($quiz) use ($siswa) {
            $leaderboard = Leaderboard::where('id_quiz', $quiz->id_quiz)
                ->where('nisn', $siswa->nisn)
                ->first();

            return [
                'id_quiz' => $quiz->id_quiz,
                'tipe_test' => $quiz->tipe_test,
                'poin' => $quiz->poin,
                'timer' => $quiz->timer,
                'tanggal' => $quiz->tanggal,
                'score' => $leaderboard ? $leaderboard->total_poin : null,
                'attempted' => $leaderboard !== null,
            ];
        });

        return response()->json([
            'message' => 'Detail materi berhasil diambil.',
            'data' => [
                'id_materi' => $materi->id_materi,
                'judul_materi' => $materi->judul_materi,
                'isi_materi' => $materi->isi_materi,
                'upload_file' => $materi->upload_file
                    ? Storage::disk('public')->url($materi->upload_file)
                    : null,
                'guru_pengajar' => $materi->adminGuru
                    ? $materi->adminGuru->nama_lengkap
                    : null,
                'pretest_completed' => $pretestCompleted,
                'quizzes' => $quizzes,
            ],
        ]);
    }

    public function serveFile(Request $request, Materi $materi): JsonResponse|Response
    {
        $siswa = $request->user();

        if ($materi->kelas !== $siswa->kelas || $materi->jurusan !== $siswa->jurusan || $materi->tahun !== $siswa->tahun) {
            return response()->json([
                'message' => 'Kamu tidak memiliki akses ke file materi ini.',
            ], 403);
        }

        $pretests = $materi->quiz()->where('tipe_test', 'pretest')->get();
        $hasPretests = $pretests->isNotEmpty();

        if ($hasPretests) {
            $pretestCompleted = Leaderboard::where('nisn', $siswa->nisn)
                ->whereIn('id_quiz', $pretests->pluck('id_quiz'))
                ->exists();

            if (! $pretestCompleted) {
                return response()->json([
                    'message' => 'Kamu harus menyelesaikan Pre-Test terlebih dahulu sebelum dapat mengakses file materi.',
                ], 403);
            }
        }

        if (! $materi->upload_file || ! Storage::disk('public')->exists($materi->upload_file)) {
            return response()->json([
                'message' => 'File materi tidak ditemukan.',
            ], 404);
        }

        return response()->file(Storage::disk('public')->path($materi->upload_file));
    }
}
