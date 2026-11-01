<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use App\Models\Materi;
use App\Models\MateriSiswa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MateriSiswaController extends Controller
{
    /**
     * Menampilkan daftar materi sesuai kelas dan jurusan siswa yang login.
     */
    public function index(): View
    {
        $siswa = auth('siswa')->user();
        $materis = Materi::with(['adminGuru', 'quiz'])
            ->where('kelas', $siswa->kelas)
            ->where('jurusan', $siswa->jurusan)
            ->where('tahun', $siswa->tahun)
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

        foreach ($materis as $index => $materi) {
            $pretest = $materi->quiz->where('tipe_test', 'pretest')->first();
            $posttest = $materi->quiz->where('tipe_test', 'posttest')->first();

            $pretestAttempt = $pretest ? $attempts->get($pretest->id_quiz) : null;
            $posttestAttempt = $posttest ? $attempts->get($posttest->id_quiz) : null;

            $hasPretest = (bool) $pretest;
            $hasPosttest = (bool) $posttest;

            $pretestDone = ! $hasPretest || ($pretestAttempt !== null);
            $posttestDone = $hasPosttest && ($posttestAttempt !== null);

            $materiSiswaRecord = $progressMap->get($materi->id_materi);
            $isDibuka = $materiSiswaRecord ? (bool) $materiSiswaRecord->is_dibuka : false;
            $isMateriSelesai = $materiSiswaRecord ? (bool) $materiSiswaRecord->is_selesai : false;

            // Hitung persentase kemajuan belajar berdasarkan kuis & penyelesaian materi
            $totalComponents = 1; // Bahan Ajar selalu ada
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

            if ($completedComponents === 0) {
                $progress = 0;
            } elseif ($completedComponents === $totalComponents) {
                $progress = 100;
            } else {
                $progress = (int) round(($completedComponents / $totalComponents) * 100);
            }

            if ($progress === 100) {
                $statusKey = 'selesai';
            } elseif ($progress > 0) {
                $statusKey = 'sedang-berjalan';
            } else {
                $statusKey = (! $hasPretest && ! $hasPosttest) ? 'semua' : 'belum-dimulai';
            }

            $materi->kode_modul = str_replace(' ', '', $siswa->kelas ?? '10').$jurusanClean.'-'.sprintf('%02d', $index + 1);
            $materi->pretest = $pretest;
            $materi->posttest = $posttest;
            $materi->pretestAttempt = $pretestAttempt;
            $materi->posttestAttempt = $posttestAttempt;
            $materi->hasPretest = $hasPretest;
            $materi->hasPosttest = $hasPosttest;
            $materi->pretestDone = $pretestDone;
            $materi->posttestDone = $posttestDone;
            $materi->isDibuka = $isDibuka;
            $materi->isMateriSelesai = $isMateriSelesai;
            $materi->progress = $progress;
            $materi->statusKey = $statusKey;
        }

        $countSemua = $materis->count();
        $countSedangBerjalan = $materis->where('statusKey', 'sedang-berjalan')->count();
        $countSelesai = $materis->where('statusKey', 'selesai')->count();
        $countBelumDimulai = $materis->where('statusKey', 'belum-dimulai')->count();

        return view('siswa.materi.index', compact(
            'siswa',
            'materis',
            'countSemua',
            'countSedangBerjalan',
            'countSelesai',
            'countBelumDimulai'
        ));
    }

    /**
     * Menampilkan detail materi beserta info akses pre-test.
     */
    public function show(Materi $materi): View|RedirectResponse
    {
        $siswa = auth('siswa')->user();

        if ($materi->kelas !== $siswa->kelas || $materi->jurusan !== $siswa->jurusan || $materi->tahun !== $siswa->tahun) {
            return redirect()
                ->route('portal.materi.index')
                ->with('error', 'Kamu tidak memiliki akses ke materi ini.');
        }

        $materi->load(['adminGuru', 'quiz.soal']);

        $pretests = $materi->quiz->where('tipe_test', 'pretest');
        $hasPretests = $pretests->isNotEmpty();
        $pretestCompleted = false;

        if ($hasPretests) {
            $pretestIds = $pretests->pluck('id_quiz');
            $pretestCompleted = Leaderboard::where('nisn', $siswa->nisn)
                ->whereIn('id_quiz', $pretestIds)
                ->exists();
        } else {
            $pretestCompleted = true;
        }

        // Catat bahwa materi telah dibuka oleh siswa
        $materiSiswaRecord = MateriSiswa::firstOrCreate(
            ['nisn' => $siswa->nisn, 'id_materi' => $materi->id_materi],
            ['is_dibuka' => true, 'is_selesai' => false]
        );

        if (! $materiSiswaRecord->is_dibuka) {
            $materiSiswaRecord->update(['is_dibuka' => true]);
        }

        return view('siswa.materi.show', compact('materi', 'pretestCompleted', 'hasPretests', 'materiSiswaRecord'));
    }

    /**
     * Menandai modul materi telah selesai dipelajari oleh siswa.
     */
    public function tandaiSelesai(Materi $materi): RedirectResponse
    {
        $siswa = auth('siswa')->user();

        if ($materi->kelas !== $siswa->kelas || $materi->jurusan !== $siswa->jurusan || $materi->tahun !== $siswa->tahun) {
            return redirect()
                ->route('portal.materi.index')
                ->with('error', 'Kamu tidak memiliki akses ke materi ini.');
        }

        $pretests = $materi->quiz()->where('tipe_test', 'pretest')->get();
        if ($pretests->isNotEmpty()) {
            $pretestCompleted = Leaderboard::where('nisn', $siswa->nisn)
                ->whereIn('id_quiz', $pretests->pluck('id_quiz'))
                ->exists();

            if (! $pretestCompleted) {
                return back()->with('error', 'Selesaikan Pre-Test terlebih dahulu sebelum menandai modul materi ini selesai.');
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

        $pesan = $isNowSelesai
            ? 'Modul materi "'.$materi->judul_materi.'" berhasil ditandai selesai dipelajari.'
            : 'Status selesai pada materi "'.$materi->judul_materi.'" berhasil dibatalkan.';

        return back()->with('success', $pesan);
    }

    /**
     * Melayani file materi untuk siswa yang sudah memenuhi syarat akses.
     */
    public function serveFile(Materi $materi): StreamedResponse|RedirectResponse
    {
        $siswa = auth('siswa')->user();

        if ($materi->kelas !== $siswa->kelas || $materi->jurusan !== $siswa->jurusan || $materi->tahun !== $siswa->tahun) {
            return redirect()
                ->route('portal.materi.index')
                ->with('error', 'Kamu tidak memiliki akses ke file materi ini.');
        }

        $pretests = $materi->quiz()->where('tipe_test', 'pretest')->get();
        $hasPretests = $pretests->isNotEmpty();

        if ($hasPretests) {
            $pretestIds = $pretests->pluck('id_quiz');
            $pretestCompleted = Leaderboard::where('nisn', $siswa->nisn)
                ->whereIn('id_quiz', $pretestIds)
                ->exists();

            if (! $pretestCompleted) {
                return redirect()
                    ->route('portal.quiz.index', $materi)
                    ->with('error', 'Kamu harus menyelesaikan Pre-Test terlebih dahulu sebelum dapat mengakses file materi.');
            }
        }

        if (! $materi->upload_file || ! Storage::disk('public')->exists($materi->upload_file)) {
            abort(404, 'File materi tidak ditemukan.');
        }

        return Storage::disk('public')->response($materi->upload_file);
    }
}
