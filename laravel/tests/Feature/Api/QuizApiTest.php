<?php

namespace Tests\Feature\Api;

use App\Models\AdminGuru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Leaderboard;
use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Quiz;
use App\Models\Soal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class QuizApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_quiz_materi_dan_filter_posttest_tanpa_jadwal(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materi = $this->buatMateri($siswa);

        $pretest = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'poin' => 20,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);
        // Posttest tanpa jam_mulai → tidak ditampilkan
        Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'posttest',
            'tanggal' => now()->addDay()->toDateString(),
        ]);
        // Posttest dengan jadwal lengkap → ditampilkan
        $posttest = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'posttest',
            'tanggal' => now()->addDays(2)->toDateString(),
            'jam_mulai' => '08:00',
        ]);
        $this->buatSoal($pretest, 'Berapa 1+1?', 'a');
        Leaderboard::create([
            'id_quiz' => $pretest->id_quiz,
            'nisn' => $siswa->nisn,
            'total_poin' => 90,
        ]);

        $response = $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/materi/{$materi->id_materi}/quiz");

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'message',
                'data' => [[
                    'id_quiz',
                    'tipe_test',
                    'poin',
                    'timer',
                    'tanggal',
                    'jumlah_soal',
                    'attempted',
                    'score',
                ]],
            ]);

        $data = collect($response->json('data'));
        $pretestItem = $data->firstWhere('id_quiz', $pretest->id_quiz);
        $this->assertTrue($pretestItem['attempted']);
        $this->assertSame(90, $pretestItem['score']);
        $this->assertSame(1, $pretestItem['jumlah_soal']);

        $posttestItem = $data->firstWhere('id_quiz', $posttest->id_quiz);
        $this->assertFalse($posttestItem['attempted']);
        $this->assertNull($posttestItem['score']);
    }

    public function test_daftar_quiz_ditolak_untuk_kelas_lain(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materiLain = $this->buatMateriLain();

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/materi/{$materiLain->id_materi}/quiz")
            ->assertForbidden()
            ->assertJsonPath('message', 'Kamu tidak memiliki akses ke materi ini.');
    }

    public function test_daftar_quiz_bisa_difilter_berdasarkan_tipe_test(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materi = $this->buatMateri($siswa);
        Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);
        Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'posttest',
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '08:00',
        ]);

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/materi/{$materi->id_materi}/quiz?tipe_test=pretest")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.tipe_test', 'pretest');
    }

    public function test_kerjakan_quiz_mengembalikan_soal_dan_pilihan(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materi = $this->buatMateri($siswa);
        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);
        $soal1 = $this->buatSoal($quiz, 'Pertanyaan pertama?', 'b');
        $soal2 = $this->buatSoal($quiz, 'Pertanyaan kedua?', 'c');

        $response = $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/quiz/{$quiz->id_quiz}");

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id_quiz',
                    'tipe_test',
                    'timer',
                    'poin',
                    'judul_materi',
                    'soals' => [['id_soal', 'pertanyaan', 'choices']],
                ],
            ])
            ->assertJsonPath('data.judul_materi', 'Materi Dasar Jaringan')
            ->assertJsonPath('data.soals.0.id_soal', $soal1->id_soal)
            ->assertJsonPath('data.soals.0.choices.a', 'Pilihan A 1')
            ->assertJsonPath('data.soals.0.choices.b', 'Pilihan B 1')
            ->assertJsonPath('data.soals.0.choices.c', 'Pilihan C 1')
            ->assertJsonPath('data.soals.0.choices.d', 'Pilihan D 1')
            ->assertJsonPath('data.soals.1.id_soal', $soal2->id_soal);
    }

    public function test_kerjakan_quiz_shuffle_mempertahankan_key_pilihan_asli(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materi = $this->buatMateri($siswa);
        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'is_shuffle' => true,
            'tanggal' => now()->toDateString(),
        ]);
        $this->buatSoal($quiz, 'Pertanyaan acak?', 'a');

        $response = $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/quiz/{$quiz->id_quiz}");

        $response->assertOk();

        $choices = $response->json('data.soals.0.choices');
        $this->assertEqualsCanonicalizing(['a', 'b', 'c', 'd'], array_keys($choices));
        $this->assertSame('Pilihan A 1', $choices['a']);
        $this->assertSame('Pilihan B 1', $choices['b']);
        $this->assertSame('Pilihan C 1', $choices['c']);
        $this->assertSame('Pilihan D 1', $choices['d']);
    }

    public function test_kerjakan_quiz_ditolak_untuk_kelas_lain(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materiLain = $this->buatMateriLain();
        $quiz = Quiz::create([
            'id_materi' => $materiLain->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/quiz/{$quiz->id_quiz}")
            ->assertForbidden()
            ->assertJsonPath('message', 'Kamu tidak memiliki akses ke quiz ini.');
    }

    public function test_kerjakan_posttest_ditolak_sebelum_waktu_mulai(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materi = $this->buatMateri($siswa);
        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'posttest',
            'tanggal' => now()->addDays(3)->toDateString(),
            'jam_mulai' => '08:00',
        ]);

        $response = $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/quiz/{$quiz->id_quiz}");

        $response->assertForbidden();
        $this->assertStringStartsWith('Post-test belum dimulai.', $response->json('message'));
    }

    public function test_kerjakan_posttest_ditolak_setelah_sudah_dikerjakan(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materi = $this->buatMateri($siswa);
        $quiz = $this->buatPosttestAktif($materi);
        Leaderboard::create([
            'id_quiz' => $quiz->id_quiz,
            'nisn' => $siswa->nisn,
            'total_poin' => 70,
        ]);

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/quiz/{$quiz->id_quiz}")
            ->assertForbidden()
            ->assertJsonPath('message', 'Anda sudah mengerjakan post-test ini dan tidak dapat mengerjakannya kembali.');
    }

    public function test_kerjakan_posttest_ditolak_jika_pretest_belum_selesai(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materi = $this->buatMateri($siswa);

        // Buat pretest untuk materi ini
        Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);

        // Buat posttest aktif
        $posttest = $this->buatPosttestAktif($materi);

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/quiz/{$posttest->id_quiz}")
            ->assertForbidden()
            ->assertJsonPath('message', 'Kamu harus menyelesaikan Pre-Test terlebih dahulu sebelum dapat mengerjakan Post-Test.');
    }

    public function test_submit_menghitung_skor_membuat_leaderboard_dan_menaikkan_poin(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materi = $this->buatMateri($siswa);
        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);
        $soal1 = $this->buatSoal($quiz, 'Benar atau salah?', 'a');
        $soal2 = $this->buatSoal($quiz, 'Satu atau dua?', 'b');

        $response = $this->withToken($this->tokenSiswa($siswa))
            ->postJson("/api/siswa/quiz/{$quiz->id_quiz}", [
                'jawaban' => [
                    $soal1->id_soal => 'a', // benar
                    $soal2->id_soal => 'd', // salah
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('benar', 1)
            ->assertJsonPath('total', 2)
            ->assertJsonPath('total_poin', 50);

        $this->assertDatabaseHas('leaderboard', [
            'id_quiz' => $quiz->id_quiz,
            'nisn' => $siswa->nisn,
            'total_poin' => 50,
        ]);
        $this->assertSame(50, $siswa->fresh()->poin);
    }

    public function test_submit_quiz_ditolak_untuk_kelas_lain(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materiLain = $this->buatMateriLain();
        $quiz = Quiz::create([
            'id_materi' => $materiLain->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);

        $this->withToken($this->tokenSiswa($siswa))
            ->postJson("/api/siswa/quiz/{$quiz->id_quiz}", ['jawaban' => []])
            ->assertForbidden()
            ->assertJsonPath('message', 'Kamu tidak memiliki akses ke quiz ini.');
    }

    public function test_submit_quiz_menolak_kunci_jawaban_tidak_valid(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materi = $this->buatMateri($siswa);
        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);
        $soal = $this->buatSoal($quiz, 'Pertanyaan?', 'a');

        $this->withToken($this->tokenSiswa($siswa))
            ->postJson("/api/siswa/quiz/{$quiz->id_quiz}", [
                'jawaban' => [$soal->id_soal => 'z'],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(["jawaban.{$soal->id_soal}"]);
    }

    public function test_submit_posttest_ditolak_setelah_sudah_dikerjakan(): void
    {
        $siswa = $this->buatSiswaAktif();
        $materi = $this->buatMateri($siswa);
        $quiz = $this->buatPosttestAktif($materi);
        Leaderboard::create([
            'id_quiz' => $quiz->id_quiz,
            'nisn' => $siswa->nisn,
            'total_poin' => 70,
        ]);

        $this->withToken($this->tokenSiswa($siswa))
            ->postJson("/api/siswa/quiz/{$quiz->id_quiz}", ['jawaban' => []])
            ->assertForbidden()
            ->assertJsonPath('message', 'Anda sudah mengerjakan post-test ini dan tidak dapat mengerjakannya kembali.');
    }

    private function buatSiswaAktif(): PenggunaSiswa
    {
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Komputer & Jaringan']);
        $kelas = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 1,
            'tahun' => '2026',
        ]);

        return PenggunaSiswa::create([
            'nisn' => '0051234561',
            'nama_lengkap' => 'Andi Pratama',
            'id_kelas' => $kelas->id_kelas,
            'password' => Hash::make('password123'),
        ]);
    }

    private function buatMateri(PenggunaSiswa $siswa): Materi
    {
        $guru = AdminGuru::create([
            'nip' => '198712345678901234',
            'nama_lengkap' => 'Budi Santoso, S.Pd',
            'password' => Hash::make('password123'),
        ]);

        return Materi::create([
            'nip' => $guru->nip,
            'id_kelas' => $siswa->id_kelas,
            'judul_materi' => 'Materi Dasar Jaringan',
            'isi_materi' => 'Isi materi dasar jaringan.',
        ]);
    }

    private function buatMateriLain(): Materi
    {
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Komputer & Jaringan']);
        $kelasLain = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 2,
            'tahun' => '2026',
        ]);
        $guru = AdminGuru::create([
            'nip' => '198712345678901235',
            'nama_lengkap' => 'Guru Kelas Lain',
            'password' => Hash::make('password123'),
        ]);

        return Materi::create([
            'nip' => $guru->nip,
            'id_kelas' => $kelasLain->id_kelas,
            'judul_materi' => 'Materi Kelas Lain',
            'isi_materi' => 'Isi materi kelas lain.',
        ]);
    }

    private function buatPosttestAktif(Materi $materi): Quiz
    {
        // Jadwal aktif: hari ini 00:00 sampai 48 jam kemudian
        return Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'posttest',
            'timer' => 2880,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '00:00',
        ]);
    }

    private function buatSoal(Quiz $quiz, string $pertanyaan, string $jawabanBenar): Soal
    {
        $soal = Soal::create([
            'id_quiz' => $quiz->id_quiz,
            'pertanyaan' => $pertanyaan,
            'jawaban_benar' => $jawabanBenar,
        ]);
        $soal->syncPilihan([
            'a' => 'Pilihan A 1',
            'b' => 'Pilihan B 1',
            'c' => 'Pilihan C 1',
            'd' => 'Pilihan D 1',
        ]);

        return $soal;
    }

    private function tokenSiswa(PenggunaSiswa $siswa): string
    {
        return $siswa->createToken('tes')->plainTextToken;
    }
}
