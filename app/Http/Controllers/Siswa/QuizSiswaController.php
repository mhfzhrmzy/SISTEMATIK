<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizSiswaController extends Controller
{
    /**
     * Menampilkan daftar kuis untuk materi tertentu beserta status pengerjaan siswa.
     */
    public function index(Materi $materi)
    {
        $materi->load('adminGuru');
        $quizzes = $materi->quiz()->with('soal')->latest()->get();

        // Filter: posttest tanpa jadwal lengkap tidak ditampilkan
        $quizzes = $quizzes->filter(function (Quiz $quiz) {
            if ($quiz->tipe_test === 'posttest') {
                return $quiz->jadwalLengkap();
            }

            return true;
        })->values();

        $siswa = auth('siswa')->user();
        $attemptedIds = Leaderboard::where('nisn', $siswa->nisn)
            ->whereIn('id_quiz', $quizzes->pluck('id_quiz'))
            ->pluck('total_poin', 'id_quiz');

        return view('siswa.quiz.index', compact('materi', 'quizzes', 'attemptedIds'));
    }

    /**
     * Menampilkan halaman pengerjaan kuis.
     */
    public function kerjakan(Quiz $quiz)
    {
        $quiz->load('soal', 'materi');

        // Penguncian waktu untuk posttest
        if ($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap()) {
            $now = now();
            $waktuMulai = $quiz->waktuMulai();
            $waktuSelesai = $quiz->waktuSelesai();

            if ($now->lt($waktuMulai)) {
                return redirect()->route('portal.quiz.index', $quiz->materi)
                    ->withErrors(['jadwal' => 'Post-test belum dimulai. Waktu mulai: '.$waktuMulai->translatedFormat('l, d M Y H:i').' WIB']);
            }

            if ($now->gt($waktuSelesai)) {
                return redirect()->route('portal.quiz.index', $quiz->materi)
                    ->withErrors(['jadwal' => 'Waktu pengerjaan post-test sudah habis.']);
            }
        }

        // Blokir pengerjaan ulang posttest jika sudah pernah dikerjakan
        if ($quiz->tipe_test === 'posttest') {
            $sudahDikerjakan = Leaderboard::where('id_quiz', $quiz->id_quiz)
                ->where('nisn', auth('siswa')->id())
                ->exists();

            if ($sudahDikerjakan) {
                return redirect()->route('portal.quiz.index', $quiz->materi)
                    ->withErrors(['jadwal' => 'Anda sudah mengerjakan post-test ini dan tidak dapat mengerjakannya kembali.']);
            }
        }

        $soals = $quiz->soal;

        if ($quiz->is_shuffle && $soals->isNotEmpty()) {
            $siswaId = auth('siswa')->id();
            $sessionKey = 'quiz_shuffle_'.$quiz->id_quiz.'_'.$siswaId;
            $shuffledData = session($sessionKey);

            if (! $shuffledData) {
                // Acak urutan soal secara unik untuk siswa ini
                $shuffledSoals = $soals->shuffle()->values();
                $shuffledData = [];

                foreach ($shuffledSoals as $soal) {
                    $choices = [
                        'a' => $soal->pilihan_a,
                        'b' => $soal->pilihan_b,
                        'c' => $soal->pilihan_c,
                        'd' => $soal->pilihan_d,
                    ];

                    // Acak urutan pilihan jawaban
                    $keys = array_keys($choices);
                    shuffle($keys);

                    $shuffledChoices = [];
                    foreach ($keys as $k) {
                        $shuffledChoices[$k] = $choices[$k];
                    }

                    $shuffledData[] = [
                        'id_soal' => $soal->id_soal,
                        'choices' => $shuffledChoices,
                    ];
                }

                session([$sessionKey => $shuffledData]);
            }

            // Terapkan susunan urutan soal & pilihan jawaban dari session
            $soalMap = $soals->keyBy('id_soal');
            $orderedSoals = collect();

            foreach ($shuffledData as $item) {
                if (isset($soalMap[$item['id_soal']])) {
                    $s = clone $soalMap[$item['id_soal']];
                    $s->display_choices = $item['choices'];
                    $orderedSoals->push($s);
                }
            }

            $quiz->setRelation('soal', $orderedSoals);
        } else {
            // Urutan normal (tidak diacak)
            foreach ($quiz->soal as $s) {
                $s->display_choices = [
                    'a' => $s->pilihan_a,
                    'b' => $s->pilihan_b,
                    'c' => $s->pilihan_c,
                    'd' => $s->pilihan_d,
                ];
            }
        }

        // Hitung sisa detik untuk posttest (untuk countdown absolut)
        $sisaDetik = null;
        if ($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap()) {
            $sisaDetik = max(0, (int) now()->diffInSeconds($quiz->waktuSelesai(), false));
        }

        return view('siswa.quiz.kerjakan', compact('quiz', 'sisaDetik'));
    }

    /**
     * Memproses dan menyimpan jawaban kuis siswa ke leaderboard.
     */
    public function submit(Request $request, Quiz $quiz)
    {
        // Penguncian waktu untuk posttest
        if ($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap()) {
            $now = now();
            $waktuMulai = $quiz->waktuMulai();
            $waktuSelesai = $quiz->waktuSelesai();

            if ($now->lt($waktuMulai)) {
                return redirect()->route('portal.quiz.index', $quiz->materi)
                    ->withErrors(['jadwal' => 'Post-test belum dimulai.']);
            }

            $batasGrace = $waktuSelesai->copy()->addSeconds(Quiz::GRACE_PERIOD_DETIK);
            if ($now->gt($batasGrace)) {
                return redirect()->route('portal.quiz.index', $quiz->materi)
                    ->withErrors(['jadwal' => 'Waktu pengerjaan sudah habis. Jawaban tidak tersimpan.']);
            }
        }

        $soals = $quiz->soal;
        $jawaban = $request->input('jawaban', []);

        $benar = 0;
        foreach ($soals as $soal) {
            if (($jawaban[$soal->id_soal] ?? null) === $soal->jawaban_benar) {
                $benar++;
            }
        }

        $totalSoal = $soals->count();
        $poinPerSoal = $totalSoal > 0 ? (100 / $totalSoal) : 0;
        $totalPoin = $totalSoal > 0 ? (int) round($benar * $poinPerSoal) : 0;

        Leaderboard::updateOrCreate(
            ['id_quiz' => $quiz->id_quiz, 'nisn' => auth('siswa')->id()],
            ['total_poin' => $totalPoin]
        );

        // Sinkronisasi akumulasi total poin ke data siswa
        $nisnSiswa = auth('siswa')->id();
        $totalAkumulasi = (int) Leaderboard::where('nisn', $nisnSiswa)->sum('total_poin');
        PenggunaSiswa::where('nisn', $nisnSiswa)->update(['poin' => $totalAkumulasi]);

        // Hapus session acak setelah kuis berhasil disubmit
        $sessionKey = 'quiz_shuffle_'.$quiz->id_quiz.'_'.auth('siswa')->id();
        session()->forget($sessionKey);

        return view('siswa.quiz.hasil', [
            'quiz' => $quiz,
            'benar' => $benar,
            'total' => $totalSoal,
            'totalPoin' => $totalPoin,
        ]);
    }
}
