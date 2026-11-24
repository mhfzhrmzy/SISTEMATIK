<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use App\Models\Materi;
use App\Models\MateriSiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MateriController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $siswa = $request->user();
        $siswa->loadMissing('kelas.jurusan');

        $materis = Materi::with(['Guru', 'quiz', 'kelas.jurusan'])
            ->where('id_kelas', $siswa->id_kelas)
            ->latest()
            ->get();

        $allQuizIds = $materis->flatMap->quiz->pluck('id_quiz')->unique();
        $attempts = Leaderboard::where('nisn', $siswa->nisn)
            ->whereIn('id_quiz', $allQuizIds)
            ->get()
            ->keyBy('id_quiz');

        $progressMap = MateriSiswa::where('nisn', $siswa->nisn)
            ->get()
            ->keyBy('id_materi');

        $jurusanClean = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $siswa->jurusan ?? 'TKJ'), 0, 4));
        $kelasPrefix = $siswa->kelas?->tingkat_romawi ?? 'X';

        $data = $materis->values()->map(function (Materi $materi, int $index) use ($attempts, $progressMap, $jurusanClean, $kelasPrefix) {
            $quizzes = $materi->quiz;
            $pretest = $quizzes->firstWhere('tipe_test', 'pretest');
            $posttest = $quizzes->firstWhere('tipe_test', 'posttest');

            $hasPretest = $pretest !== null;
            $hasPosttest = $posttest !== null;

            $pretestAttempt = $hasPretest ? $attempts->get($pretest->id_quiz) : null;
            $posttestAttempt = $hasPosttest ? $attempts->get($posttest->id_quiz) : null;

            $pretestCompleted = ! $hasPretest || ($pretestAttempt !== null);
            $posttestCompleted = $hasPosttest && ($posttestAttempt !== null);

            $materiSiswaRecord = $progressMap->get($materi->id_materi);
            $isDibuka = $materiSiswaRecord ? (bool) $materiSiswaRecord->is_dibuka : false;
            $isMateriSelesai = $materiSiswaRecord ? (bool) $materiSiswaRecord->is_selesai : false;

            // Hitung persentase progress belajar (0 - 100%)
            $totalComponents = 1; // Bahan Ajar
            if ($hasPretest) {
                $totalComponents++;
            }
            if ($hasPosttest) {
                $totalComponents++;
            }

            $completedComponents = 0;
            if ($hasPretest && $pretestAttempt !== null) {
                $completedComponents++;
            }
            if ($isMateriSelesai) {
                $completedComponents++;
            }
            if ($hasPosttest && $posttestAttempt !== null) {
                $completedComponents++;
            }

            $progress = (int) round(($completedComponents / $totalComponents) * 100);
            $kodeModul = $kelasPrefix.$jurusanClean.'-'.sprintf('%02d', $index + 1);

            return [
                'id_materi' => $materi->id_materi,
                'kode_modul' => $kodeModul,
                'judul_materi' => $materi->judul_materi,
                'isi_materi' => $materi->isi_materi,
                'kelas' => $materi->kelas?->nama_kelas,
                'jurusan' => $materi->jurusan,
                'upload_file' => $materi->upload_file
                    ? Storage::disk('public')->url($materi->upload_file)
                    : null,
                'guru_pengajar' => $materi->Guru
                    ? $materi->Guru->nama_lengkap
                    : null,
                'quiz_count' => $quizzes->count(),
                'pretest_count' => $quizzes->where('tipe_test', 'pretest')->count(),
                'has_pretest' => $hasPretest,
                'id_pretest' => $pretest?->id_quiz,
                'pretest_completed' => $pretestCompleted,
                'has_posttest' => $hasPosttest,
                'id_posttest' => $posttest?->id_quiz,
                'posttest_completed' => $posttestCompleted,
                'is_dibuka' => $isDibuka,
                'is_selesai' => $isMateriSelesai,
                'progress' => $progress,
            ];
        });

        return response()->json([
            'message' => 'Daftar materi berhasil diambil.',
            'data' => $data,
        ]);
    }

    public function show(Request $request, Materi $materi): JsonResponse
    {
        $siswa = $request->user();

        if ((int) $materi->id_kelas !== (int) $siswa->id_kelas) {
            return response()->json([
                'message' => 'Kamu tidak memiliki akses ke materi ini.',
            ], 403);
        }

        $materi->load(['Guru', 'quiz.soal']);

        $pretests = $materi->quiz->where('tipe_test', 'pretest');
        $pretestCompleted = false;

        if ($pretests->isNotEmpty()) {
            $pretestCompleted = Leaderboard::where('nisn', $siswa->nisn)
                ->whereIn('id_quiz', $pretests->pluck('id_quiz'))
                ->exists();
        } else {
            $pretestCompleted = true;
        }

        // Catat bahwa materi dibuka oleh siswa
        $materiSiswaRecord = MateriSiswa::firstOrCreate(
            ['nisn' => $siswa->nisn, 'id_materi' => $materi->id_materi],
            ['is_dibuka' => true, 'is_selesai' => false]
        );
        if (! $materiSiswaRecord->is_dibuka) {
            $materiSiswaRecord->update(['is_dibuka' => true]);
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
                'guru_pengajar' => $materi->Guru
                    ? $materi->Guru->nama_lengkap
                    : null,
                'pretest_completed' => $pretestCompleted,
                'is_dibuka' => (bool) $materiSiswaRecord->is_dibuka,
                'is_selesai' => (bool) $materiSiswaRecord->is_selesai,
                'selesai_at' => $materiSiswaRecord->selesai_at?->toIso8601String(),
                'quizzes' => $quizzes,
            ],
        ]);
    }

    public function tandaiSelesai(Request $request, Materi $materi): JsonResponse
    {
        $siswa = $request->user();

        if ((int) $materi->id_kelas !== (int) $siswa->id_kelas) {
            return response()->json([
                'message' => 'Kamu tidak memiliki akses ke materi ini.',
            ], 403);
        }

        $pretests = $materi->quiz()->where('tipe_test', 'pretest')->get();
        if ($pretests->isNotEmpty()) {
            $pretestCompleted = Leaderboard::where('nisn', $siswa->nisn)
                ->whereIn('id_quiz', $pretests->pluck('id_quiz'))
                ->exists();

            if (! $pretestCompleted) {
                return response()->json([
                    'message' => 'Selesaikan Pre-Test terlebih dahulu sebelum menandai modul materi ini selesai.',
                ], 403);
            }
        }

        $materiSiswaRecord = MateriSiswa::firstOrCreate(
            ['nisn' => $siswa->nisn, 'id_materi' => $materi->id_materi],
            ['is_dibuka' => true, 'is_selesai' => false]
        );

        $isNowSelesai = ! $materiSiswaRecord->is_selesai;
        $materiSiswaRecord->update([
            'is_dibuka' => true,
            'is_selesai' => $isNowSelesai,
            'selesai_at' => $isNowSelesai ? now() : null,
        ]);

        return response()->json([
            'message' => $isNowSelesai
                ? 'Modul materi "'.$materi->judul_materi.'" berhasil ditandai selesai dipelajari.'
                : 'Status selesai pada materi "'.$materi->judul_materi.'" berhasil dibatalkan.',
            'is_selesai' => $isNowSelesai,
            'selesai_at' => $materiSiswaRecord->selesai_at?->toIso8601String(),
        ]);
    }

    public function serveFile(Request $request, Materi $materi): JsonResponse|Response
    {
        $siswa = $request->user();

        if ((int) $materi->id_kelas !== (int) $siswa->id_kelas) {
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
