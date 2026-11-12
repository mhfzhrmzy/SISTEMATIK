<?php

namespace Tests\Feature;

use App\Models\AdminGuru;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Quiz;
use App\Models\Sertifikat;
use App\Models\Soal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuruManagementTest extends TestCase
{
    use RefreshDatabase;

    private AdminGuru $guru;

    private Kelas $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->guru = AdminGuru::create([
            'nip' => '198501012010011001',
            'nama_lengkap' => 'Budi Santoso, S.Kom.',
            'password' => bcrypt('password123'),
        ]);

        $this->kelas = Kelas::findOrCreateByNama('Teknik Komputer & Jaringan', 10, 1, '2026');
    }

    public function test_guru_can_access_materi_index(): void
    {
        $response = $this->actingAs($this->guru, 'admin')
            ->get(route('materi.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Bahan Ajar');
    }

    public function test_guru_can_create_materi(): void
    {
        $file = UploadedFile::fake()->create('modul_jaringan.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->guru, 'admin')
            ->post(route('materi.store'), [
                'judul_materi' => 'Pengenalan Jaringan Komputer',
                'nip' => $this->guru->nip,
                'kelas' => '10 1',
                'tahun' => 2026,
                'jurusan' => 'Teknik Komputer & Jaringan',
                'isi_materi' => 'Pengantar topologi jaringan dan IP address.',
                'upload_file' => $file,
            ]);

        $response->assertRedirect(route('materi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('materi', [
            'judul_materi' => 'Pengenalan Jaringan Komputer',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
        ]);
    }

    public function test_guru_can_update_materi(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Awal',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi lama',
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->put(route('materi.update', $materi), [
                'judul_materi' => 'Materi Diperbarui',
                'nip' => $this->guru->nip,
                'kelas' => '10 1',
                'tahun' => 2026,
                'jurusan' => 'Teknik Komputer & Jaringan',
                'isi_materi' => 'Deskripsi baru',
            ]);

        $response->assertRedirect(route('materi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('materi', [
            'id_materi' => $materi->id_materi,
            'judul_materi' => 'Materi Diperbarui',
            'isi_materi' => 'Deskripsi baru',
        ]);
    }

    public function test_guru_can_delete_materi(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Hapus',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi materi hapus',
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->delete(route('materi.destroy', $materi));

        $response->assertRedirect(route('materi.index'));
        $this->assertDatabaseMissing('materi', [
            'id_materi' => $materi->id_materi,
        ]);
    }

    public function test_guru_can_access_quiz_index(): void
    {
        $response = $this->actingAs($this->guru, 'admin')
            ->get(route('admin.quiz.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Quiz');
    }

    public function test_guru_can_create_quiz(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Quiz Test',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi quiz test',
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->post(route('admin.quiz.store'), [
                'id_materi' => $materi->id_materi,
                'tipe_test' => 'pretest',
                'timer' => 30,
            ]);

        $response->assertRedirect(route('admin.quiz.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('quiz', [
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'timer' => 30,
        ]);
    }

    public function test_guru_can_create_posttest_quiz(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Posttest Test',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi posttest test',
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->post(route('admin.quiz.store'), [
                'id_materi' => $materi->id_materi,
                'tipe_test' => 'posttest',
                'timer' => 45,
                'tanggal' => now()->addDay()->toDateString(),
                'jam_mulai' => '10:00',
            ]);

        $response->assertRedirect(route('admin.quiz.index'));
        $this->assertDatabaseHas('quiz', [
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'posttest',
            'timer' => 45,
        ]);
    }

    public function test_guru_can_update_jadwal_posttest(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Jadwal Test',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi jadwal test',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'posttest',
            'timer' => 30,
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '08:00',
        ]);

        $newDate = now()->addDays(2)->toDateString();

        $response = $this->actingAs($this->guru, 'admin')
            ->patch(route('admin.quiz.jadwal', $quiz), [
                'tanggal' => $newDate,
                'jam_mulai' => '09:30',
                'timer' => 40,
            ]);

        $response->assertRedirect(route('admin.quiz.index'));
        $freshQuiz = $quiz->fresh();
        $this->assertEquals($newDate, $freshQuiz->tanggal->format('Y-m-d'));
        $this->assertEquals('09:30', $freshQuiz->jam_mulai);
        $this->assertEquals(40, $freshQuiz->timer);
    }

    public function test_guru_can_delete_quiz(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Hapus Quiz',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi hapus quiz',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'timer' => 20,
            'tanggal' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->delete(route('admin.quiz.destroy', $quiz));

        $response->assertRedirect(route('admin.quiz.index'));
        $this->assertDatabaseMissing('quiz', [
            'id_quiz' => $quiz->id_quiz,
        ]);
    }

    public function test_guru_can_create_soal_with_pilihan_jawaban(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Soal Test',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi soal test',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'timer' => 20,
            'tanggal' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->post(route('admin.soal.store', $quiz), [
                'soals' => [
                    [
                        'pertanyaan' => 'Apa kepanjangan dari LAN?',
                        'pilihan_a' => 'Local Area Network',
                        'pilihan_b' => 'Large Area Network',
                        'pilihan_c' => 'Low Access Node',
                        'pilihan_d' => 'Link Area Node',
                        'jawaban_benar' => 'a',
                    ],
                ],
            ]);

        $response->assertRedirect(route('admin.soal.index', $quiz));
        $this->assertDatabaseHas('soal', [
            'id_quiz' => $quiz->id_quiz,
            'pertanyaan' => 'Apa kepanjangan dari LAN?',
            'jawaban_benar' => 'a',
        ]);

        $soal = Soal::where('id_quiz', $quiz->id_quiz)->first();
        $this->assertDatabaseHas('pilihan_jawaban', [
            'id_soal' => $soal->id_soal,
            'label' => 'a',
            'teks_pilihan' => 'Local Area Network',
        ]);
    }

    public function test_guru_can_update_soal_and_pilihan(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Update Soal',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi update soal',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'timer' => 20,
            'tanggal' => now()->toDateString(),
        ]);

        $soal = $quiz->soal()->create([
            'pertanyaan' => 'Pertanyaan awal?',
            'jawaban_benar' => 'a',
        ]);
        $soal->syncPilihan([
            'pilihan_a' => 'Opsi A',
            'pilihan_b' => 'Opsi B',
            'pilihan_c' => 'Opsi C',
            'pilihan_d' => 'Opsi D',
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->put(route('admin.soal.update', $soal), [
                'pertanyaan' => 'Pertanyaan diubah?',
                'pilihan_a' => 'Opsi A baru',
                'pilihan_b' => 'Opsi B baru',
                'pilihan_c' => 'Opsi C baru',
                'pilihan_d' => 'Opsi D baru',
                'jawaban_benar' => 'b',
            ]);

        $response->assertRedirect(route('admin.soal.index', $quiz));
        $this->assertDatabaseHas('soal', [
            'id_soal' => $soal->id_soal,
            'pertanyaan' => 'Pertanyaan diubah?',
            'jawaban_benar' => 'b',
        ]);
        $this->assertEquals('Opsi A baru', $soal->fresh()->pilihan_a);
        $this->assertEquals('Opsi B baru', $soal->fresh()->pilihan_b);
    }

    public function test_guru_can_import_soal_csv(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Import Soal',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi import soal',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'timer' => 20,
            'tanggal' => now()->toDateString(),
        ]);

        $csvContent = "pertanyaan,pilihan_a,pilihan_b,pilihan_c,pilihan_d,jawaban_benar\n";
        $csvContent .= "Soal CSV 1?,Jawaban A,Jawaban B,Jawaban C,Jawaban D,a\n";
        $csvContent .= "Soal CSV 2?,Opsi 1,Opsi 2,Opsi 3,Opsi 4,c\n";

        $file = UploadedFile::fake()->createWithContent('soal.csv', $csvContent);

        $response = $this->actingAs($this->guru, 'admin')
            ->post(route('admin.soal.import', $quiz), [
                'csv_file' => $file,
            ]);

        $response->assertRedirect(route('admin.soal.index', $quiz));
        $this->assertEquals(2, $quiz->soal()->count());
        $this->assertDatabaseHas('pilihan_jawaban', [
            'label' => 'a',
            'teks_pilihan' => 'Jawaban A',
        ]);
    }

    public function test_guru_can_toggle_shuffle_quiz(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Shuffle',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi shuffle',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'timer' => 20,
            'tanggal' => now()->toDateString(),
            'is_shuffle' => false,
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->patch(route('admin.quiz.toggle-shuffle', $quiz));

        $response->assertRedirect(route('admin.soal.index', $quiz));
        $this->assertTrue($quiz->fresh()->is_shuffle);
    }

    public function test_guru_can_delete_soal(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Materi Delete Soal',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi delete soal',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'timer' => 20,
            'tanggal' => now()->toDateString(),
        ]);

        $soal = $quiz->soal()->create([
            'pertanyaan' => 'Soal untuk dihapus?',
            'jawaban_benar' => 'a',
        ]);
        $soal->syncPilihan([
            'pilihan_a' => 'A',
            'pilihan_b' => 'B',
            'pilihan_c' => 'C',
            'pilihan_d' => 'D',
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->delete(route('admin.soal.destroy', $soal));

        $response->assertRedirect(route('admin.soal.index', $quiz));
        $this->assertDatabaseMissing('soal', ['id_soal' => $soal->id_soal]);
        $this->assertDatabaseMissing('pilihan_jawaban', ['id_soal' => $soal->id_soal]);
    }

    public function test_guru_can_fetch_siswa_json_by_filter(): void
    {
        $siswa1 = PenggunaSiswa::create([
            'nisn' => '0051112221',
            'nama_lengkap' => 'Ahmad Siswa 1',
            'password' => bcrypt('password123'),
            'id_kelas' => $this->kelas->id_kelas,
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->getJson(route('sertifikat.siswa-json', [
                'kelas' => '10 1',
                'jurusan' => 'Teknik Komputer & Jaringan',
            ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'nisn' => '0051112221',
            'nama_lengkap' => 'Ahmad Siswa 1',
            'kelas' => '10 1',
            'jurusan' => 'Teknik Komputer & Jaringan',
        ]);
    }

    public function test_guru_can_create_and_store_sertifikat(): void
    {
        $siswa = PenggunaSiswa::create([
            'nisn' => '0051112222',
            'nama_lengkap' => 'Budi Siswa 2',
            'password' => bcrypt('password123'),
            'id_kelas' => $this->kelas->id_kelas,
        ]);

        // 1. Select student in create flow
        $response = $this->actingAs($this->guru, 'admin')
            ->post(route('sertifikat.create'), [
                'student_ids' => [$siswa->nisn],
            ]);

        $response->assertStatus(200);
        $response->assertSee('Budi Siswa 2');

        // 2. Upload sertifikat
        $file = UploadedFile::fake()->create('sertifikat.pdf', 200, 'application/pdf');

        $storeResponse = $this->actingAs($this->guru, 'admin')
            ->post(route('sertifikat.store'), [
                'judul_sertifikat' => 'Sertifikat Kelulusan Jaringan Komputer',
                'penerbit' => 'SMKN 2 Jember',
                'file_sertifikat' => $file,
            ]);

        $storeResponse->assertRedirect(route('sertifikat.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('sertifikat', [
            'nisn' => $siswa->nisn,
            'judul_sertifikat' => 'Sertifikat Kelulusan Jaringan Komputer',
            'penerbit' => 'SMKN 2 Jember',
        ]);
    }

    public function test_guru_can_delete_sertifikat(): void
    {
        $siswa = PenggunaSiswa::create([
            'nisn' => '0051112223',
            'nama_lengkap' => 'Citra Siswa 3',
            'password' => bcrypt('password123'),
            'id_kelas' => $this->kelas->id_kelas,
        ]);

        $sertifikat = Sertifikat::create([
            'nisn' => $siswa->nisn,
            'judul_sertifikat' => 'Sertifikat Dihapus',
            'file_sertifikat' => 'sertifikat/dummy.pdf',
            'tipe_sertifikat' => 'mandiri',
        ]);

        $response = $this->actingAs($this->guru, 'admin')
            ->delete(route('sertifikat.destroy', $sertifikat));

        $response->assertRedirect();
        $this->assertDatabaseMissing('sertifikat', [
            'id_sertifikat' => $sertifikat->id_sertifikat,
        ]);
    }

    public function test_guru_can_access_leaderboard_with_filter(): void
    {
        $response = $this->actingAs($this->guru, 'admin')
            ->get(route('admin.leaderboard.index', [
                'kelas' => '10 1',
                'jurusan' => 'Teknik Komputer & Jaringan',
            ]));

        $response->assertStatus(200);
        $response->assertSee('Leaderboard dan Peringkat Nilai Siswa');
    }

    public function test_guru_can_view_profile_and_change_password(): void
    {
        $response = $this->actingAs($this->guru, 'admin')
            ->get(route('admin.profil'));

        $response->assertStatus(200);
        $response->assertSee($this->guru->nama_lengkap);

        $pwResponse = $this->actingAs($this->guru, 'admin')
            ->post(route('admin.profil.password'), [
                'password_lama' => 'password123',
                'password_baru' => 'newpassword123',
            ]);

        $pwResponse->assertRedirect();
        $pwResponse->assertSessionHas('success');

        $this->assertTrue(Hash::check('newpassword123', $this->guru->fresh()->password));
    }
}
