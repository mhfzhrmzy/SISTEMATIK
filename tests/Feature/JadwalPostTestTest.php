<?php

namespace Tests\Feature;

use App\Models\AdminGuru;
use App\Models\Leaderboard;
use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Quiz;
use App\Models\Soal;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalPostTestTest extends TestCase
{
    use RefreshDatabase;

    private AdminGuru $guru;

    private PenggunaSiswa $siswa;

    private Materi $materi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = AdminGuru::create([
            'nip' => '198501012010011099',
            'nama_lengkap' => 'Guru Jadwal Test',
            'email' => 'gurujadwal@sekolah.sch.id',
            'password' => 'password123',
            'role' => 'guru',
        ]);

        $this->siswa = PenggunaSiswa::create([
            'nisn' => '0060000001',
            'nama_lengkap' => 'Siswa Jadwal Test',
            'email' => 'siswajadwal@sekolah.sch.id',
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
            'password' => 'password123',
        ]);

        $this->materi = Materi::create([
            'judul_materi' => 'Jaringan Jadwal Test',
            'isi_materi' => 'Konten jadwal test',
            'nip' => $this->guru->nip,
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset time
        parent::tearDown();
    }

    /**
     * Helper: buat soal untuk quiz.
     */
    private function buatSoal(Quiz $quiz, int $jumlah = 10): void
    {
        for ($i = 1; $i <= $jumlah; $i++) {
            Soal::create([
                'id_quiz' => $quiz->id_quiz,
                'pertanyaan' => "Pertanyaan $i",
                'pilihan_a' => 'A',
                'pilihan_b' => 'B',
                'pilihan_c' => 'C',
                'pilihan_d' => 'D',
                'jawaban_benar' => 'a',
            ]);
        }
    }

    /**
     * 1. Store posttest tanpa jam_mulai → gagal validasi.
     */
    public function test_store_posttest_tanpa_jam_mulai_gagal_validasi(): void
    {
        $response = $this->actingAs($this->guru, 'admin')->post(route('admin.quiz.store'), [
            'id_materi' => $this->materi->id_materi,
            'tipe_test' => 'posttest',
            'timer' => 30,
            'tanggal' => now()->addDay()->toDateString(),
        ]);

        $response->assertSessionHasErrors('jam_mulai');
        $this->assertDatabaseMissing('quiz', ['id_materi' => $this->materi->id_materi, 'tipe_test' => 'posttest']);
    }

    /**
     * 2. Store posttest dengan waktu yang sudah lewat → gagal validasi.
     */
    public function test_store_posttest_waktu_sudah_lewat_gagal_validasi(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-26 10:00:00'));

        $response = $this->actingAs($this->guru, 'admin')->post(route('admin.quiz.store'), [
            'id_materi' => $this->materi->id_materi,
            'tipe_test' => 'posttest',
            'timer' => 30,
            'tanggal' => '2026-10-26',
            'jam_mulai' => '08:00',
        ]);

        $response->assertSessionHasErrors('jam_mulai');
        $this->assertDatabaseMissing('quiz', ['id_materi' => $this->materi->id_materi, 'tipe_test' => 'posttest']);
    }

    /**
     * 2b. Store posttest dengan tanggal yang sudah lewat → gagal validasi.
     */
    public function test_store_posttest_tanggal_sudah_lewat_gagal_validasi(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-26 10:00:00'));

        $response = $this->actingAs($this->guru, 'admin')->post(route('admin.quiz.store'), [
            'id_materi' => $this->materi->id_materi,
            'tipe_test' => 'posttest',
            'timer' => 30,
            'tanggal' => '2026-10-25', // Hari kemarin
            'jam_mulai' => '11:00',
        ]);

        $response->assertSessionHasErrors('tanggal');
        $this->assertDatabaseMissing('quiz', ['id_materi' => $this->materi->id_materi, 'tipe_test' => 'posttest']);
    }

    /**
     * 3. Store posttest valid → data tersimpan dengan benar.
     */
    public function test_store_posttest_valid_tersimpan(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-26 07:00:00'));

        $response = $this->actingAs($this->guru, 'admin')->post(route('admin.quiz.store'), [
            'id_materi' => $this->materi->id_materi,
            'tipe_test' => 'posttest',
            'timer' => 45,
            'tanggal' => '2026-10-27',
            'jam_mulai' => '08:00',
        ]);

        $response->assertRedirect(route('admin.quiz.index'));

        $quiz = Quiz::where('id_materi', $this->materi->id_materi)
            ->where('tipe_test', 'posttest')
            ->first();

        $this->assertNotNull($quiz);
        $this->assertStringStartsWith('08:00', $quiz->jam_mulai);
        $this->assertEquals('2026-10-27', $quiz->tanggal->format('Y-m-d'));
        $this->assertEquals(45, $quiz->timer);
        $this->assertTrue($quiz->jadwalLengkap());
        $this->assertNotNull($quiz->waktuMulai());
        $this->assertNotNull($quiz->waktuSelesai());
        $this->assertEquals(
            $quiz->waktuMulai()->copy()->addMinutes(45)->toDateTimeString(),
            $quiz->waktuSelesai()->toDateTimeString()
        );
    }

    /**
     * 4. Posttest tanpa jadwal lengkap → tidak muncul di daftar quiz siswa.
     */
    public function test_posttest_tanpa_jadwal_tidak_muncul_ke_siswa(): void
    {
        // Quiz posttest tanpa jam_mulai
        $quiz = Quiz::create([
            'id_materi' => $this->materi->id_materi,
            'nip' => $this->guru->nip,
            'tipe_test' => 'posttest',
            'poin' => 100,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => null,
        ]);
        $this->buatSoal($quiz, 3);

        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('portal.quiz.index', $this->materi));

        $response->assertStatus(200);
        $response->assertDontSee('Post-Test');
    }

    /**
     * 5. Sebelum waktu_mulai → kerjakan ditolak dan submit ditolak.
     */
    public function test_sebelum_waktu_mulai_kerjakan_dan_submit_ditolak(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-27 07:00:00'));

        $quiz = Quiz::create([
            'id_materi' => $this->materi->id_materi,
            'nip' => $this->guru->nip,
            'tipe_test' => 'posttest',
            'poin' => 100,
            'timer' => 60,
            'tanggal' => '2026-10-27',
            'jam_mulai' => '08:00',
        ]);
        $this->buatSoal($quiz, 3);

        // Kerjakan ditolak
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('portal.quiz.kerjakan', $quiz));
        $response->assertRedirect(route('portal.quiz.index', $this->materi));
        $response->assertSessionHasErrors('jadwal');

        // Submit ditolak
        $response = $this->actingAs($this->siswa, 'siswa')
            ->post(route('portal.quiz.submit', $quiz), [
                'jawaban' => [],
            ]);
        $response->assertRedirect(route('portal.quiz.index', $this->materi));
        $this->assertDatabaseMissing('leaderboard', ['id_quiz' => $quiz->id_quiz]);
    }

    /**
     * 6. Di dalam window → kerjakan 200 dan submit berhasil.
     */
    public function test_dalam_window_kerjakan_dan_submit_berhasil(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-27 08:30:00'));

        $quiz = Quiz::create([
            'id_materi' => $this->materi->id_materi,
            'nip' => $this->guru->nip,
            'tipe_test' => 'posttest',
            'poin' => 100,
            'timer' => 60,
            'tanggal' => '2026-10-27',
            'jam_mulai' => '08:00',
        ]);
        $this->buatSoal($quiz, 3);
        $soals = $quiz->soal;

        // Kerjakan berhasil
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('portal.quiz.kerjakan', $quiz));
        $response->assertStatus(200);

        // Submit berhasil
        $jawaban = [];
        foreach ($soals as $soal) {
            $jawaban[$soal->id_soal] = 'a'; // Semua benar
        }

        $response = $this->actingAs($this->siswa, 'siswa')
            ->post(route('portal.quiz.submit', $quiz), [
                'jawaban' => $jawaban,
            ]);

        $this->assertDatabaseHas('leaderboard', [
            'id_quiz' => $quiz->id_quiz,
            'nisn' => $this->siswa->nisn,
            'total_poin' => 100,
        ]);
    }

    /**
     * 7. Submit 5 detik setelah waktu_selesai (masih grace) → berhasil.
     */
    public function test_submit_dalam_grace_period_berhasil(): void
    {
        $quiz = Quiz::create([
            'id_materi' => $this->materi->id_materi,
            'nip' => $this->guru->nip,
            'tipe_test' => 'posttest',
            'poin' => 100,
            'timer' => 60,
            'tanggal' => '2026-10-27',
            'jam_mulai' => '08:00',
        ]);
        $this->buatSoal($quiz, 3);
        $soals = $quiz->soal;

        // 5 detik setelah 09:00 (waktu_selesai)
        Carbon::setTestNow(Carbon::parse('2026-10-27 09:00:05'));

        $jawaban = [];
        foreach ($soals as $soal) {
            $jawaban[$soal->id_soal] = 'a';
        }

        $response = $this->actingAs($this->siswa, 'siswa')
            ->post(route('portal.quiz.submit', $quiz), [
                'jawaban' => $jawaban,
            ]);

        $this->assertDatabaseHas('leaderboard', [
            'id_quiz' => $quiz->id_quiz,
            'nisn' => $this->siswa->nisn,
            'total_poin' => 100,
        ]);
    }

    /**
     * 8. Submit 15 detik setelah waktu_selesai (lewat grace) → ditolak.
     */
    public function test_submit_lewat_grace_period_ditolak(): void
    {
        $quiz = Quiz::create([
            'id_materi' => $this->materi->id_materi,
            'nip' => $this->guru->nip,
            'tipe_test' => 'posttest',
            'poin' => 100,
            'timer' => 60,
            'tanggal' => '2026-10-27',
            'jam_mulai' => '08:00',
        ]);
        $this->buatSoal($quiz, 3);
        $soals = $quiz->soal;

        // 15 detik setelah 09:00 (melewati grace 10 detik)
        Carbon::setTestNow(Carbon::parse('2026-10-27 09:00:15'));

        $jawaban = [];
        foreach ($soals as $soal) {
            $jawaban[$soal->id_soal] = 'a';
        }

        $response = $this->actingAs($this->siswa, 'siswa')
            ->post(route('portal.quiz.submit', $quiz), [
                'jawaban' => $jawaban,
            ]);

        $response->assertRedirect(route('portal.quiz.index', $this->materi));
        $this->assertDatabaseMissing('leaderboard', ['id_quiz' => $quiz->id_quiz]);
    }

    /**
     * 9. Sebagian soal terjawab (7 dari 10, semua benar) → total_poin = 70.
     */
    public function test_sebagian_soal_terjawab_skor_benar(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-27 08:30:00'));

        $quiz = Quiz::create([
            'id_materi' => $this->materi->id_materi,
            'nip' => $this->guru->nip,
            'tipe_test' => 'posttest',
            'poin' => 100,
            'timer' => 60,
            'tanggal' => '2026-10-27',
            'jam_mulai' => '08:00',
        ]);
        $this->buatSoal($quiz, 10);
        $soals = $quiz->soal;

        // Jawab 7 dari 10 soal (semua benar)
        $jawaban = [];
        $count = 0;
        foreach ($soals as $soal) {
            if ($count < 7) {
                $jawaban[$soal->id_soal] = 'a'; // benar
            }
            // 3 sisanya tidak dijawab (kosong = salah)
            $count++;
        }

        $this->actingAs($this->siswa, 'siswa')
            ->post(route('portal.quiz.submit', $quiz), [
                'jawaban' => $jawaban,
            ]);

        $this->assertDatabaseHas('leaderboard', [
            'id_quiz' => $quiz->id_quiz,
            'nisn' => $this->siswa->nisn,
            'total_poin' => 70,
        ]);
    }

    /**
     * 10. Pretest tetap bisa dikerjakan kapan pun.
     */
    public function test_pretest_bisa_dikerjakan_kapanpun(): void
    {
        $quiz = Quiz::create([
            'id_materi' => $this->materi->id_materi,
            'nip' => $this->guru->nip,
            'tipe_test' => 'pretest',
            'poin' => 100,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);
        $this->buatSoal($quiz, 3);
        $soals = $quiz->soal;

        // Kerjakan berhasil
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('portal.quiz.kerjakan', $quiz));
        $response->assertStatus(200);

        // Submit berhasil
        $jawaban = [];
        foreach ($soals as $soal) {
            $jawaban[$soal->id_soal] = 'a';
        }

        $this->actingAs($this->siswa, 'siswa')
            ->post(route('portal.quiz.submit', $quiz), [
                'jawaban' => $jawaban,
            ]);

        $this->assertDatabaseHas('leaderboard', [
            'id_quiz' => $quiz->id_quiz,
            'nisn' => $this->siswa->nisn,
            'total_poin' => 100,
        ]);
    }

    /**
     * 11. admin.quiz.jadwal hanya untuk guru, dan ditolak bila ada leaderboard.
     */
    public function test_update_jadwal_role_guru_dan_ditolak_jika_sudah_dikerjakan(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-26 07:00:00'));

        $quiz = Quiz::create([
            'id_materi' => $this->materi->id_materi,
            'nip' => $this->guru->nip,
            'tipe_test' => 'posttest',
            'poin' => 100,
            'timer' => 60,
            'tanggal' => '2026-10-27',
            'jam_mulai' => '08:00',
        ]);

        // Siswa tidak bisa akses route guru
        $response = $this->actingAs($this->siswa, 'siswa')
            ->patch(route('admin.quiz.jadwal', $quiz), [
                'tanggal' => '2026-10-28',
                'jam_mulai' => '09:00',
                'timer' => 90,
            ]);
        $response->assertForbidden();

        // Guru bisa update jadwal
        $response = $this->actingAs($this->guru, 'admin')
            ->patch(route('admin.quiz.jadwal', $quiz), [
                'tanggal' => '2026-10-28',
                'jam_mulai' => '09:00',
                'timer' => 90,
            ]);
        $response->assertRedirect(route('admin.quiz.index'));

        $quiz->refresh();
        $this->assertStringStartsWith('09:00', $quiz->jam_mulai);
        $this->assertEquals(90, $quiz->timer);

        // Tambah data leaderboard → update ditolak
        Leaderboard::create([
            'id_quiz' => $quiz->id_quiz,
            'nisn' => $this->siswa->nisn,
            'total_poin' => 80,
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->patch(route('admin.quiz.jadwal', $quiz), [
                'tanggal' => '2026-10-29',
                'jam_mulai' => '10:00',
                'timer' => 60,
            ]);
        $response->assertSessionHasErrors('jadwal');
    }

    /**
     * 12. GET /admin/quiz menampilkan input jam mulai + estimasi ditutup untuk posttest.
     */
    public function test_admin_quiz_index_menampilkan_input_jam_mulai(): void
    {
        $response = $this->actingAs($this->guru, 'admin')
            ->get(route('admin.quiz.index'));

        $response->assertStatus(200);
        $response->assertSee('Jam Mulai Post-Test');
        $response->assertSee('Estimasi Ditutup');
        $response->assertSee('input-jam-mulai', false);
        $response->assertSee('input-estimasi-tutup', false);
    }
}
