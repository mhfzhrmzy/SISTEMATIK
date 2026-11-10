<?php

namespace Database\Seeders;

use App\Models\Materi;
use App\Models\PilihanJawaban;
use App\Models\Quiz;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class SoalSeeder extends Seeder
{
    public function run(): void
    {
        $materi = Materi::first();

        if (! $materi) {
            return;
        }

        // Buat quiz pretest untuk materi pertama
        $quiz = Quiz::firstOrCreate(
            ['id_materi' => $materi->id_materi, 'tipe_test' => 'pretest'],
            [
                'poin' => 10,
                'timer' => 30,
                'is_shuffle' => false,
                'tanggal' => now()->toDateString(),
            ]
        );

        $soals = [
            [
                'pertanyaan' => 'Apa kepanjangan dari LAN?',
                'jawaban_benar' => 'a',
                'pilihan' => [
                    'a' => 'Local Area Network',
                    'b' => 'Large Area Network',
                    'c' => 'Long Area Network',
                    'd' => 'Low Area Network',
                ],
            ],
            [
                'pertanyaan' => 'Perangkat yang digunakan untuk menghubungkan dua jaringan berbeda disebut?',
                'jawaban_benar' => 'c',
                'pilihan' => [
                    'a' => 'Switch',
                    'b' => 'Hub',
                    'c' => 'Router',
                    'd' => 'Modem',
                ],
            ],
            [
                'pertanyaan' => 'Topologi jaringan yang menghubungkan semua komputer ke satu kabel utama disebut?',
                'jawaban_benar' => 'b',
                'pilihan' => [
                    'a' => 'Star',
                    'b' => 'Bus',
                    'c' => 'Ring',
                    'd' => 'Mesh',
                ],
            ],
        ];

        foreach ($soals as $data) {
            $soal = Soal::firstOrCreate(
                ['id_quiz' => $quiz->id_quiz, 'pertanyaan' => $data['pertanyaan']],
                ['jawaban_benar' => $data['jawaban_benar']]
            );

            foreach ($data['pilihan'] as $label => $teks) {
                PilihanJawaban::firstOrCreate(
                    ['id_soal' => $soal->id_soal, 'label' => $label],
                    ['teks_pilihan' => $teks]
                );
            }
        }
    }
}
