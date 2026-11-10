<?php

namespace Tests\Feature;

use App\Models\AdminGuru;
use App\Models\Leaderboard;
use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuizLeaderboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_materi_file(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('modul.pdf', 100);
        $path = $file->store('materi', 'public');

        $admin = AdminGuru::create([
            'nip' => '198501012010011005',
            'nama_lengkap' => 'Guru Test',
            'email' => 'gurutest@sekolah.sch.id',
            'password' => 'password123',
            'role' => 'guru',
        ]);

        $materi = Materi::create([
            'judul_materi' => 'Matematika Dasar',
            'nip' => $admin->nip,
            'isi_materi' => 'Konten materi',
            'upload_file' => $path,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('materi.show', $materi));
        $response->assertStatus(200);
    }

    public function test_quiz_creation_defaults_tanggal_to_today(): void
    {
        $admin = AdminGuru::create([
            'nip' => '198501012010011006',
            'nama_lengkap' => 'Guru Test 2',
            'email' => 'gurutest2@sekolah.sch.id',
            'password' => 'password123',
            'role' => 'guru',
        ]);

        $materi = Materi::create([
            'judul_materi' => 'Fisika Dasar',
            'isi_materi' => 'Isi materi fisika dasar',
            'nip' => $admin->nip,
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.quiz.store'), [
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'poin' => 10,
            'timer' => 30,
        ]);

        $this->assertDatabaseHas('quiz', [
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->startOfDay()->toDateTimeString(),
        ]);

        $response->assertRedirect(route('admin.quiz.index'));
    }

    public function test_admin_can_import_csv_questions(): void
    {
        $admin = AdminGuru::create([
            'nip' => '198501012010011007',
            'nama_lengkap' => 'Guru Test 3',
            'email' => 'gurutest3@sekolah.sch.id',
            'password' => 'password123',
            'role' => 'guru',
        ]);

        $materi = Materi::create(['judul_materi' => 'Biologi', 'isi_materi' => 'Isi materi biologi', 'nip' => $admin->nip]);
        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'nip' => $admin->nip,
            'tipe_test' => 'pretest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        $csvContent = "pertanyaan,pilihan_a,pilihan_b,pilihan_c,pilihan_d,jawaban_benar\n".
                      "Berapa 1+1?,1,2,3,4,b\n".
                      "Apa warna daun?,Hijau,Biru,Merah,Kuning,a\n";

        $file = UploadedFile::fake()->createWithContent('soal.csv', $csvContent);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.soal.import', $quiz), [
            'csv_file' => $file,
        ]);

        $this->assertDatabaseHas('soal', [
            'id_quiz' => $quiz->id_quiz,
            'pertanyaan' => 'Berapa 1+1?',
            'jawaban_benar' => 'b',
        ]);

        $response->assertRedirect(route('admin.soal.index', $quiz));
    }

    public function test_leaderboard_page_renders_successfully(): void
    {
        $siswa = PenggunaSiswa::create([
            'nisn' => '0059999999',
            'nama_lengkap' => 'Siswa Pintar',
            'email' => 'pintar@sekolah.sch.id',
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
            'password' => 'password123',
        ]);

        $response = $this->actingAs($siswa, 'siswa')->get(route('leaderboard.index'));
        $response->assertStatus(200);
        $response->assertSee('Leaderboard');
    }

    public function test_guru_can_filter_leaderboard_by_kelas_and_jurusan(): void
    {
        $admin = AdminGuru::create([
            'nip' => '198501012010011009',
            'nama_lengkap' => 'Guru Leaderboard',
            'email' => 'guruleaderboard@sekolah.sch.id',
            'password' => 'password123',
            'role' => 'guru',
        ]);

        $siswaA = PenggunaSiswa::create([
            'nisn' => '0051111111',
            'nama_lengkap' => 'Siswa Kelas 10 TKJ',
            'email' => 'siswaA@sekolah.sch.id',
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
            'password' => 'password123',
        ]);

        $siswaB = PenggunaSiswa::create([
            'nisn' => '0052222222',
            'nama_lengkap' => 'Siswa Kelas 11 Mesin',
            'email' => 'siswaB@sekolah.sch.id',
            'kelas' => '11',
            'jurusan' => 'Teknik Pemesinan',
            'password' => 'password123',
        ]);

        $materi = Materi::create([
            'judul_materi' => 'Jaringan Komputer Dasar',
            'isi_materi' => 'Isi materi',
            'nip' => $admin->nip,
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'nip' => $admin->nip,
            'tipe_test' => 'posttest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        Leaderboard::create([
            'id_quiz' => $quiz->id_quiz,
            'nisn' => $siswaA->nisn,
            'total_poin' => 90,
        ]);

        Leaderboard::create([
            'id_quiz' => $quiz->id_quiz,
            'nisn' => $siswaB->nisn,
            'total_poin' => 60,
        ]);

        // Filter kelas 10 & TKJ
        $response = $this->actingAs($admin, 'admin')->get(route('leaderboard.index', [
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Siswa Kelas 10 TKJ');
        $response->assertDontSee('Siswa Kelas 11 Mesin');
        $response->assertSee('Teknik Komputer & Jaringan');
        $response->assertSee('Kelas 10');
    }

    public function test_guru_can_view_leaderboard_permateri_with_pretest_and_posttest(): void
    {
        $admin = AdminGuru::create([
            'nip' => '198501012010011010',
            'nama_lengkap' => 'Guru Materi Test',
            'email' => 'gurumateri@sekolah.sch.id',
            'password' => 'password123',
            'role' => 'guru',
        ]);

        $siswa = PenggunaSiswa::create([
            'nisn' => '0053333333',
            'nama_lengkap' => 'Siswa Penguji',
            'email' => 'penguji@sekolah.sch.id',
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
            'password' => 'password123',
        ]);

        $materi = Materi::create([
            'judul_materi' => 'Sistem Operasi',
            'isi_materi' => 'Konten OS',
            'nip' => $admin->nip,
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
        ]);

        $quizPre = Quiz::create([
            'id_materi' => $materi->id_materi,
            'nip' => $admin->nip,
            'tipe_test' => 'pretest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        $quizPost = Quiz::create([
            'id_materi' => $materi->id_materi,
            'nip' => $admin->nip,
            'tipe_test' => 'posttest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        Leaderboard::create([
            'id_quiz' => $quizPre->id_quiz,
            'nisn' => $siswa->nisn,
            'total_poin' => 50,
        ]);

        Leaderboard::create([
            'id_quiz' => $quizPost->id_quiz,
            'nisn' => $siswa->nisn,
            'total_poin' => 100,
        ]);

        // Cek Leaderboard Pre-Test per materi
        $responsePre = $this->actingAs($admin, 'admin')->get(route('leaderboard.index', [
            'tipe_leaderboard' => 'permateri',
            'materi_id' => $materi->id_materi,
            'jurusan' => 'Teknik Komputer & Jaringan',
            'tipe_test' => 'pretest',
        ]));

        $responsePre->assertStatus(200);
        $responsePre->assertSee('Siswa Penguji');
        $responsePre->assertSee('50');
        $responsePre->assertSee('Pre-Test');

        // Cek Leaderboard Post-Test per materi
        $responsePost = $this->actingAs($admin, 'admin')->get(route('leaderboard.index', [
            'tipe_leaderboard' => 'permateri',
            'materi_id' => $materi->id_materi,
            'jurusan' => 'Teknik Komputer & Jaringan',
            'tipe_test' => 'posttest',
        ]));

        $responsePost->assertStatus(200);
        $responsePost->assertSee('Siswa Penguji');
        $responsePost->assertSee('100');
        $responsePost->assertSee('Post-Test');
    }

    public function test_admin_leaderboard_route_is_accessible(): void
    {
        $admin = AdminGuru::create([
            'nip' => '198501012010011011',
            'nama_lengkap' => 'Guru Admin Route',
            'email' => 'adminroute@sekolah.sch.id',
            'password' => 'password123',
            'role' => 'guru',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.leaderboard.index'));
        $response->assertStatus(200);
        $response->assertSee('Leaderboard');
    }

    public function test_siswa_only_sees_posttest_for_their_own_kelas_and_jurusan(): void
    {
        $admin = AdminGuru::create([
            'nip' => '198501012010011012',
            'nama_lengkap' => 'Guru Pengampu Siswa',
            'email' => 'pengampusiswa@sekolah.sch.id',
            'password' => 'password123',
            'role' => 'guru',
        ]);

        $siswaTKJ = PenggunaSiswa::create([
            'nisn' => '0054444441',
            'nama_lengkap' => 'Siswa Kelas 10 TKJ Satu',
            'email' => 'tkj1@sekolah.sch.id',
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
            'password' => 'password123',
        ]);

        $siswaMesin = PenggunaSiswa::create([
            'nisn' => '0054444442',
            'nama_lengkap' => 'Siswa Kelas 11 Mesin Dua',
            'email' => 'mesin2@sekolah.sch.id',
            'kelas' => '11',
            'jurusan' => 'Teknik Pemesinan',
            'password' => 'password123',
        ]);

        $materiTKJ = Materi::create([
            'judul_materi' => 'Jaringan Komputer Siswa',
            'isi_materi' => 'Konten Jarkom',
            'nip' => $admin->nip,
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
        ]);

        $materiMesin = Materi::create([
            'judul_materi' => 'Teknik Bubut Siswa',
            'isi_materi' => 'Konten Bubut',
            'nip' => $admin->nip,
            'kelas' => '11',
            'jurusan' => 'Teknik Pemesinan',
        ]);

        $quizTKJPre = Quiz::create([
            'id_materi' => $materiTKJ->id_materi,
            'nip' => $admin->nip,
            'tipe_test' => 'pretest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        $quizTKJPost = Quiz::create([
            'id_materi' => $materiTKJ->id_materi,
            'nip' => $admin->nip,
            'tipe_test' => 'posttest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        $quizMesinPost = Quiz::create([
            'id_materi' => $materiMesin->id_materi,
            'nip' => $admin->nip,
            'tipe_test' => 'posttest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        // Nilai Pre-Test Siswa TKJ = 40 (tidak boleh muncul di leaderboard siswa)
        Leaderboard::create([
            'id_quiz' => $quizTKJPre->id_quiz,
            'nisn' => $siswaTKJ->nisn,
            'total_poin' => 40,
        ]);

        // Nilai Post-Test Siswa TKJ = 95 (harus muncul)
        Leaderboard::create([
            'id_quiz' => $quizTKJPost->id_quiz,
            'nisn' => $siswaTKJ->nisn,
            'total_poin' => 95,
        ]);

        // Nilai Post-Test Siswa Mesin = 100 (tidak boleh muncul karena kelas/jurusan lain)
        Leaderboard::create([
            'id_quiz' => $quizMesinPost->id_quiz,
            'nisn' => $siswaMesin->nisn,
            'total_poin' => 100,
        ]);

        // Login sebagai Siswa TKJ
        $response = $this->actingAs($siswaTKJ, 'siswa')->get(route('leaderboard.index'));

        $response->assertStatus(200);
        $response->assertSee('Siswa Kelas 10 TKJ Satu');
        $response->assertSee('0054444441');
        // Tidak boleh melihat siswa dari kelas/jurusan lain
        $response->assertDontSee('Siswa Kelas 11 Mesin Dua');
        // Tidak boleh melihat nilai pre-test (40)
        $response->assertDontSee('Pre-Test');
        $response->assertSee('Post-Test');
    }

    public function test_siswa_cannot_tamper_kelas_or_jurusan_via_query_params(): void
    {
        $siswaTKJ = PenggunaSiswa::create([
            'nisn' => '0055555551',
            'nama_lengkap' => 'Siswa Anti Tamper',
            'email' => 'tamper@sekolah.sch.id',
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
            'password' => 'password123',
        ]);

        $siswaMesin = PenggunaSiswa::create([
            'nisn' => '0055555552',
            'nama_lengkap' => 'Siswa Mesin Tersembunyi',
            'email' => 'sembunyi@sekolah.sch.id',
            'kelas' => '11',
            'jurusan' => 'Teknik Pemesinan',
            'password' => 'password123',
        ]);

        // Siswa TKJ mencoba mengintip Kelas 11 Mesin via URL parameter
        $response = $this->actingAs($siswaTKJ, 'siswa')->get(route('leaderboard.index', [
            'kelas' => '11',
            'jurusan' => 'Teknik Pemesinan',
            'tipe_test' => 'pretest',
        ]));

        $response->assertStatus(200);
        // Tetap terkunci ke kelas siswa tersebut (10 TKJ)
        $response->assertDontSee('Siswa Mesin Tersembunyi');
        $response->assertSee('Kelas 10 • Teknik Komputer & Jaringan');
    }
}
