<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AdminGuru;
use App\Models\Materi;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizManajemenController extends Controller
{
    /**
     * Menampilkan daftar kuis dan form tambah kuis.
     */
    public function index(): View
    {
        $quizzes = Quiz::with(['materi.adminGuru', 'soal'])->latest()->get();
        $materis = Materi::with(['adminGuru', 'kelas'])->orderBy('judul_materi')->get();
        $admins = AdminGuru::orderBy('nama_lengkap', 'asc')->get();

        // Kombinasi id_materi => [tipe_test, ...] yang sudah ada (untuk mencegah duplikat)
        $existingCombinations = Quiz::select('id_materi', 'tipe_test')
            ->get()
            ->groupBy('id_materi')
            ->map(fn ($items) => $items->pluck('tipe_test')->toArray());

        return view('superadmin.quiz.index', compact('quizzes', 'materis', 'admins', 'existingCombinations'));
    }

    /**
     * Menyimpan kuis baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $labelTipe = $request->input('tipe_test') === 'pretest' ? 'pre-test' : 'post-test';

        $rules = [
            'id_materi' => ['required', 'exists:materi,id_materi'],
            'nip' => ['nullable', 'exists:admin_guru,nip'],
            'tipe_test' => [
                'required',
                'in:pretest,posttest',
                Rule::unique('quiz')->where(fn ($query) => $query->where('id_materi', $request->id_materi)),
            ],
            'poin' => ['nullable', 'integer', 'min:1'],
            'timer' => ['required', 'integer', 'min:1', 'max:120'],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai' => ['required', 'date_format:H:i'],
        ];

        $messages = [
            'tipe_test.unique' => 'Quiz tipe ini sudah ada untuk materi yang dipilih. Setiap materi hanya boleh memiliki satu pre-test dan satu post-test.',
            'tanggal.after_or_equal' => "Tanggal {$labelTipe} hanya bisa hari ini atau masa depan.",
            'jam_mulai.required' => "Jam mulai {$labelTipe} wajib diisi.",
            'jam_mulai.date_format' => 'Format jam mulai harus HH:MM.',
            'nip.exists' => 'Guru yang dipilih tidak valid.',
        ];

        $validated = $request->validate($rules, $messages);

        // Jika nip tidak dipilih, gunakan nip dari materi terkait
        if (empty($validated['nip'])) {
            $materi = Materi::find($validated['id_materi']);
            $validated['nip'] = $materi?->nip;
        }

        // Validasi waktu mulai quiz harus di masa depan
        $waktuMulai = Carbon::parse($validated['tanggal'].' '.$validated['jam_mulai']);
        if ($waktuMulai->lte(now())) {
            return back()->withErrors([
                'jam_mulai' => "Waktu mulai {$labelTipe} sudah lewat. Pilih waktu yang masih ada di depan.",
            ])->withInput();
        }

        $validated['poin'] = 100; // Otomatis total 100 poin (poin per soal = 100 / jumlah soal)
        unset($validated['nip']);

        Quiz::create($validated);

        return redirect()->route('superadmin.quiz.index')->with('success', 'Quiz berhasil dibuat.');
    }

    /**
     * Mengubah jadwal quiz (tanggal, jam_mulai, timer).
     */
    public function updateJadwal(Request $request, Quiz $quiz): RedirectResponse
    {
        $labelTipe = $quiz->tipe_test === 'pretest' ? 'Pre-test' : 'Post-test';

        // Tolak jika sudah ada siswa yang mengerjakan
        if ($quiz->leaderboard()->exists()) {
            return back()->withErrors([
                'jadwal' => "{$labelTipe} sudah dimulai oleh siswa, jadwal tidak bisa diubah.",
            ]);
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'timer' => ['required', 'integer', 'min:1', 'max:120'],
        ], [
            'tanggal.after_or_equal' => "Tanggal {$labelTipe} hanya bisa hari ini atau masa depan.",
            'jam_mulai.required' => "Jam mulai {$labelTipe} wajib diisi.",
            'jam_mulai.date_format' => 'Format jam mulai harus HH:MM.',
        ]);

        $waktuMulai = Carbon::parse($validated['tanggal'].' '.$validated['jam_mulai']);
        if ($waktuMulai->lte(now())) {
            return back()->withErrors([
                'jam_mulai' => "Waktu mulai {$labelTipe} sudah lewat. Pilih waktu yang masih ada di depan.",
            ])->withInput();
        }

        $quiz->update($validated);

        return redirect()->route('superadmin.quiz.index')->with('success', "Jadwal {$labelTipe} berhasil diperbarui.");
    }

    /**
     * Menghapus kuis beserta seluruh soal di dalamnya.
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()->route('superadmin.quiz.index')->with('success', 'Quiz berhasil dihapus.');
    }
}
