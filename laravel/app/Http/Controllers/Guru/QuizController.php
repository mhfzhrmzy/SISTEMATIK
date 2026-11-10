<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizController extends Controller
{
    /**
     * Menampilkan daftar kuis dan form tambah kuis.
     */
    public function index(): View
    {
        $quizzes = Quiz::with(['materi', 'adminGuru', 'soal'])->latest()->get();
        $materis = Materi::orderBy('judul_materi')->get();

        // Kombinasi id_materi => [tipe_test, ...] yang sudah ada (untuk mencegah duplikat)
        $existingCombinations = Quiz::select('id_materi', 'tipe_test')
            ->get()
            ->groupBy('id_materi')
            ->map(fn ($items) => $items->pluck('tipe_test')->toArray());

        return view('guru.quiz.index', compact('quizzes', 'materis', 'existingCombinations'));
    }

    /**
     * Menyimpan kuis baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $isPosttest = $request->input('tipe_test') === 'posttest';

        $rules = [
            'id_materi' => ['required', 'exists:materi,id_materi'],
            'tipe_test' => [
                'required',
                'in:pretest,posttest',
                Rule::unique('quiz')->where(fn ($query) => $query->where('id_materi', $request->id_materi)),
            ],
            'poin' => ['nullable', 'integer', 'min:1'],
            'timer' => ['required', 'integer', 'min:1', 'max:120'],
        ];

        if ($isPosttest) {
            $rules['tanggal'] = ['required', 'date', 'after_or_equal:today'];
            $rules['jam_mulai'] = ['required', 'date_format:H:i'];
        }

        $messages = [
            'tipe_test.unique' => 'Quiz tipe ini sudah ada untuk materi yang dipilih. Setiap materi hanya boleh memiliki satu pre-test dan satu post-test.',
            'tanggal.after_or_equal' => 'Tanggal post-test hanya bisa hari ini atau masa depan.',
            'jam_mulai.required' => 'Jam mulai post-test wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai harus HH:MM.',
        ];

        $validated = $request->validate($rules, $messages);

        // Validasi waktu mulai posttest harus di masa depan
        if ($isPosttest) {
            $waktuMulai = Carbon::parse($validated['tanggal'].' '.$validated['jam_mulai']);
            if ($waktuMulai->lte(now())) {
                return back()->withErrors([
                    'jam_mulai' => 'Waktu mulai post-test sudah lewat. Pilih waktu yang masih ada di depan.',
                ])->withInput();
            }
        }

        $validated['poin'] = 100; // Otomatis total 100 poin (poin per soal = 100 / jumlah soal)
        $validated['nip'] = auth('admin')->id();

        if (! $isPosttest) {
            $validated['tanggal'] = now()->toDateString();
            // Pretest tidak punya jam_mulai
            unset($validated['jam_mulai']);
        }

        Quiz::create($validated);

        return redirect()->route('admin.quiz.index')->with('success', 'Quiz berhasil dibuat.');
    }

    /**
     * Mengubah jadwal posttest (tanggal, jam_mulai, timer).
     */
    public function updateJadwal(Request $request, Quiz $quiz): RedirectResponse
    {
        if ($quiz->tipe_test !== 'posttest') {
            return back()->withErrors(['tipe_test' => 'Hanya post-test yang bisa diubah jadwalnya.']);
        }

        // Tolak jika sudah ada siswa yang mengerjakan
        if ($quiz->leaderboard()->exists()) {
            return back()->withErrors([
                'jadwal' => 'Post-test sudah dimulai oleh siswa, jadwal tidak bisa diubah.',
            ]);
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'timer' => ['required', 'integer', 'min:1', 'max:120'],
        ], [
            'tanggal.after_or_equal' => 'Tanggal post-test hanya bisa hari ini atau masa depan.',
            'jam_mulai.required' => 'Jam mulai post-test wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai harus HH:MM.',
        ]);

        $waktuMulai = Carbon::parse($validated['tanggal'].' '.$validated['jam_mulai']);
        if ($waktuMulai->lte(now())) {
            return back()->withErrors([
                'jam_mulai' => 'Waktu mulai post-test sudah lewat. Pilih waktu yang masih ada di depan.',
            ])->withInput();
        }

        $quiz->update($validated);

        return redirect()->route('admin.quiz.index')->with('success', 'Jadwal post-test berhasil diperbarui.');
    }

    /**
     * Menghapus kuis beserta seluruh soal di dalamnya.
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()->route('admin.quiz.index')->with('success', 'Quiz berhasil dihapus.');
    }
}
