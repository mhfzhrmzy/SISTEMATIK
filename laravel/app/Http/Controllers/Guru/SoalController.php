<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Soal;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SoalController extends Controller
{
    /**
     * Menampilkan daftar soal untuk kuis tertentu.
     */
    public function index(Quiz $quiz): View
    {
        $soals = $quiz->soal()->latest()->get();

        return view('guru.soal.index', compact('quiz', 'soals'));
    }

    /**
     * Menyimpan soal-soal baru ke database dengan pengecekan duplikat pertanyaan.
     */
    public function store(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'soals' => ['required', 'array', 'min:1'],
            'soals.*.pertanyaan' => ['required', 'string'],
            'soals.*.pilihan_a' => ['required', 'string', 'max:500'],
            'soals.*.pilihan_b' => ['required', 'string', 'max:500'],
            'soals.*.pilihan_c' => ['required', 'string', 'max:500'],
            'soals.*.pilihan_d' => ['required', 'string', 'max:500'],
            'soals.*.jawaban_benar' => ['required', 'in:a,b,c,d,A,B,C,D'],
        ]);

        // Kumpulkan pertanyaan yang sudah ada untuk cek duplikat
        $existingPertanyaans = $quiz->soal()
            ->pluck('pertanyaan')
            ->map(fn ($p) => strtolower(trim($p)))
            ->toArray();

        $duplicates = [];
        $added = 0;

        foreach ($validated['soals'] as $soalData) {
            $normalizedPertanyaan = strtolower(trim($soalData['pertanyaan']));

            if (in_array($normalizedPertanyaan, $existingPertanyaans, true)) {
                $duplicates[] = $soalData['pertanyaan'];

                continue;
            }

            $soalData['jawaban_benar'] = strtolower($soalData['jawaban_benar']);
            $quiz->soal()->create($soalData);
            $existingPertanyaans[] = $normalizedPertanyaan;
            $added++;
        }

        $message = $added > 0
            ? "{$added} soal berhasil ditambahkan."
            : 'Tidak ada soal baru yang ditambahkan.';

        if (! empty($duplicates)) {
            $message .= ' '.count($duplicates).' soal dilewati karena pertanyaannya sudah ada: "'.implode('", "', $duplicates).'".';
        }

        return redirect()
            ->route('admin.soal.index', $quiz)
            ->with($added > 0 ? 'success' : 'error', $message);
    }

    /**
     * Mengimpor soal dari file CSV. Hanya diizinkan jika quiz belum memiliki soal.
     */
    public function importCsv(Request $request, Quiz $quiz): RedirectResponse
    {
        if ($quiz->soal()->exists()) {
            return back()->with('error', 'Import CSV hanya bisa dilakukan sekali. Quiz ini sudah memiliki soal. Tambahkan soal baru secara manual.');
        }

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [
            'csv_file.required' => 'File CSV/Spreadsheet wajib diunggah.',
            'csv_file.mimes' => 'Format file harus berupa .csv',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return back()->with('error', 'Gagal membaca file CSV.');
        }

        $firstRow = fgetcsv($handle, 2000, ',');

        // Deteksi pemisah koma atau titik koma
        if ($firstRow && count($firstRow) === 1 && str_contains($firstRow[0], ';')) {
            rewind($handle);
            fgetcsv($handle, 2000, ';');
            $delimiter = ';';
        } else {
            $delimiter = ',';
        }

        $importedCount = 0;

        while (($row = fgetcsv($handle, 2000, $delimiter)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $pertanyaan = trim($row[0] ?? '');
            $pilihanA = trim($row[1] ?? '');
            $pilihanB = trim($row[2] ?? '');
            $pilihanC = trim($row[3] ?? '');
            $pilihanD = trim($row[4] ?? '');
            $jawabanBenar = strtolower(trim($row[5] ?? 'a'));

            if ($pertanyaan !== '' && $pilihanA !== '' && $pilihanB !== '') {
                if (! in_array($jawabanBenar, ['a', 'b', 'c', 'd'])) {
                    $jawabanBenar = 'a';
                }

                $quiz->soal()->create([
                    'pertanyaan' => $pertanyaan,
                    'pilihan_a' => $pilihanA,
                    'pilihan_b' => $pilihanB,
                    'pilihan_c' => $pilihanC,
                    'pilihan_d' => $pilihanD,
                    'jawaban_benar' => $jawabanBenar,
                ]);

                $importedCount++;
            }
        }

        fclose($handle);

        return redirect()
            ->route('admin.soal.index', $quiz)
            ->with('success', "Berhasil meng-import {$importedCount} soal dari file spreadsheet/CSV.");
    }

    /**
     * Mengunduh template CSV kosong untuk pengisian soal.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_soal_quiz.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'jawaban_benar']);
            fputcsv($handle, ['Berapakah hasil dari 5 + 5?', '8', '9', '10', '11', 'c']);
            fputcsv($handle, ['Apakah ibukota dari Indonesia?', 'Bandung', 'Jakarta', 'Surabaya', 'Medan', 'b']);
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Menampilkan form edit soal.
     */
    public function edit(Soal $soal): View
    {
        return view('guru.soal.edit', compact('soal'));
    }

    /**
     * Memperbarui data soal yang sudah ada.
     */
    public function update(Request $request, Soal $soal): RedirectResponse
    {
        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'pilihan_a' => ['required', 'string', 'max:500'],
            'pilihan_b' => ['required', 'string', 'max:500'],
            'pilihan_c' => ['required', 'string', 'max:500'],
            'pilihan_d' => ['required', 'string', 'max:500'],
            'jawaban_benar' => ['required', 'in:a,b,c,d,A,B,C,D'],
        ]);

        $validated['jawaban_benar'] = strtolower($validated['jawaban_benar']);

        $soal->update($validated);

        return redirect()->route('admin.soal.index', $soal->id_quiz)->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Menghapus soal dari database.
     */
    public function destroy(Soal $soal): RedirectResponse
    {
        $idQuiz = $soal->id_quiz;
        $soal->delete();

        return redirect()->route('admin.soal.index', $idQuiz)->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Mengaktifkan atau menonaktifkan pengacakan soal & jawaban untuk kuis.
     */
    public function toggleShuffle(Quiz $quiz): RedirectResponse
    {
        $quiz->update([
            'is_shuffle' => ! $quiz->is_shuffle,
        ]);

        $status = $quiz->is_shuffle ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.soal.index', $quiz)->with('success', "Fitur acak soal & jawaban untuk siswa berhasil {$status}.");
    }
}
