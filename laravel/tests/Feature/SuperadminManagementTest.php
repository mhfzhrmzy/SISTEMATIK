<?php

namespace Tests\Feature;

use App\Models\AdminGuru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Quiz;
use App\Models\Soal;
use App\Models\SuperAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperadminManagementTest extends TestCase
{
    use RefreshDatabase;

    private SuperAdmin $superadmin;

    private Jurusan $jurusan;

    private Kelas $kelas;

    private AdminGuru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->superadmin = SuperAdmin::create([
            'nip' => '123456789012345678',
            'nama_lengkap' => 'Super Administrator',
            'password' => bcrypt('password123'),
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer & Jaringan',
        ]);

        $this->kelas = Kelas::create([
            'id_jurusan' => $this->jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 1,
            'tahun' => '2026',
        ]);

        $this->guru = AdminGuru::create([
            'nip' => '198501012010011001',
            'nama_lengkap' => 'Budi Santoso, S.Pd',
            'password' => bcrypt('password123'),
            'dibuat_oleh' => $this->superadmin->nip,
        ]);
    }

    public function test_superadmin_dashboard_can_be_rendered(): void
    {
        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->get(route('superadmin.index', ['menu' => 'dashboard']));

        $response->assertStatus(200);
        $response->assertSee('TOTAL AKUN GURU');
        $response->assertSee('TOTAL AKUN SISWA');
    }

    public function test_superadmin_guru_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->get(route('superadmin.index', ['menu' => 'guru']));

        $response->assertStatus(200);
        $response->assertSee('Kelola Akun Guru');
        $response->assertSee($this->guru->nama_lengkap);
    }

    public function test_superadmin_can_create_guru(): void
    {
        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->post(route('admin.store'), [
                'from' => 'superadmin',
                'nip' => '198803152012022002',
                'nama_lengkap' => 'Siti Rahmawati, M.Kom',
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('superadmin.index', ['menu' => 'guru']));
        $this->assertDatabaseHas('admin_guru', [
            'nip' => '198803152012022002',
            'nama_lengkap' => 'Siti Rahmawati, M.Kom',
            'dibuat_oleh' => $this->superadmin->nip,
        ]);
    }

    public function test_superadmin_can_update_guru(): void
    {
        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->put(route('admin.update', $this->guru), [
                'from' => 'superadmin',
                'nip' => $this->guru->nip,
                'nama_lengkap' => 'Budi Santoso, M.Pd',
            ]);

        $response->assertRedirect(route('superadmin.index', ['menu' => 'guru']));
        $this->assertDatabaseHas('admin_guru', [
            'nip' => $this->guru->nip,
            'nama_lengkap' => 'Budi Santoso, M.Pd',
        ]);
    }

    public function test_superadmin_can_delete_guru(): void
    {
        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->delete(route('admin.destroy', $this->guru), [
                'from' => 'superadmin',
            ]);

        $response->assertRedirect(route('superadmin.index', ['menu' => 'guru']));
        $this->assertDatabaseMissing('admin_guru', [
            'nip' => $this->guru->nip,
        ]);
    }

    public function test_superadmin_siswa_page_can_be_rendered(): void
    {
        $siswa = PenggunaSiswa::create([
            'nisn' => '0051234561',
            'nama_lengkap' => 'Andi Pratama',
            'id_kelas' => $this->kelas->id_kelas,
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->get(route('superadmin.index', ['menu' => 'siswa']));

        $response->assertStatus(200);
        $response->assertSee('Kelola Akun Siswa');
        $response->assertSee($siswa->nama_lengkap);
    }

    public function test_superadmin_can_create_siswa(): void
    {
        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->post(route('siswa.store'), [
                'from' => 'superadmin',
                'nisn' => '0051234571',
                'nama_lengkap' => 'Andi Sultan',
                'kelas' => '10 1',
                'tahun' => '2026',
                'jurusan' => 'Teknik Komputer & Jaringan',
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('superadmin.index', ['menu' => 'siswa']));
        $this->assertDatabaseHas('pengguna_siswa', [
            'nisn' => '0051234571',
            'nama_lengkap' => 'Andi Sultan',
            'id_kelas' => $this->kelas->id_kelas,
            'dibuat_oleh' => $this->superadmin->nip,
        ]);
    }

    public function test_superadmin_can_update_siswa(): void
    {
        $siswa = PenggunaSiswa::create([
            'nisn' => '0051234561',
            'nama_lengkap' => 'Andi Pratama',
            'id_kelas' => $this->kelas->id_kelas,
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->put(route('siswa.update', $siswa), [
                'from' => 'superadmin',
                'nisn' => $siswa->nisn,
                'nama_lengkap' => 'Andi Pratama Putra',
                'kelas' => '10 1',
                'tahun' => '2026',
                'jurusan' => 'Teknik Komputer & Jaringan',
            ]);

        $response->assertRedirect(route('superadmin.index', ['menu' => 'siswa']));
        $this->assertDatabaseHas('pengguna_siswa', [
            'nisn' => $siswa->nisn,
            'nama_lengkap' => 'Andi Pratama Putra',
        ]);
    }

    public function test_superadmin_can_delete_siswa(): void
    {
        $siswa = PenggunaSiswa::create([
            'nisn' => '0051234561',
            'nama_lengkap' => 'Andi Pratama',
            'id_kelas' => $this->kelas->id_kelas,
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->delete(route('siswa.destroy', $siswa), [
                'from' => 'superadmin',
            ]);

        $response->assertRedirect(route('superadmin.index', ['menu' => 'siswa']));
        $this->assertDatabaseMissing('pengguna_siswa', [
            'nisn' => $siswa->nisn,
        ]);
    }

    public function test_superadmin_materi_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->get(route('superadmin.materi.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Bahan Ajar');
    }

    public function test_superadmin_can_create_materi(): void
    {
        $file = UploadedFile::fake()->create('modul.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->post(route('superadmin.materi.store'), [
                'judul_materi' => 'Dasar Jaringan Komputer',
                'nip' => $this->guru->nip,
                'kelas' => '10 1',
                'tahun' => '2026',
                'jurusan' => 'Teknik Komputer & Jaringan',
                'isi_materi' => 'Pengenalan jaringan komputer dasar.',
                'upload_file' => $file,
            ]);

        $response->assertRedirect(route('superadmin.materi.index'));
        $this->assertDatabaseHas('materi', [
            'judul_materi' => 'Dasar Jaringan Komputer',
            'nip' => $this->guru->nip,
            'nip_superadmin' => $this->superadmin->nip,
            'id_kelas' => $this->kelas->id_kelas,
        ]);
    }

    public function test_superadmin_can_update_materi(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Dasar Jaringan Komputer',
            'nip' => $this->guru->nip,
            'nip_superadmin' => $this->superadmin->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi lama',
        ]);

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->put(route('superadmin.materi.update', $materi), [
                'judul_materi' => 'Dasar Jaringan Komputer Lanjutan',
                'nip' => $this->guru->nip,
                'kelas' => '10 1',
                'tahun' => '2026',
                'jurusan' => 'Teknik Komputer & Jaringan',
                'isi_materi' => 'Deskripsi baru',
            ]);

        $response->assertRedirect(route('superadmin.materi.index'));
        $this->assertDatabaseHas('materi', [
            'id_materi' => $materi->id_materi,
            'judul_materi' => 'Dasar Jaringan Komputer Lanjutan',
            'isi_materi' => 'Deskripsi baru',
        ]);
    }

    public function test_superadmin_can_delete_materi(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Dasar Jaringan Komputer',
            'nip' => $this->guru->nip,
            'nip_superadmin' => $this->superadmin->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi materi',
        ]);

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->delete(route('superadmin.materi.destroy', $materi));

        $response->assertRedirect(route('superadmin.materi.index'));
        $this->assertDatabaseMissing('materi', [
            'id_materi' => $materi->id_materi,
        ]);
    }

    public function test_superadmin_quiz_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->get(route('superadmin.quiz.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Quiz');
    }

    public function test_superadmin_can_create_quiz(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Dasar Jaringan Komputer',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi materi',
        ]);

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->post(route('superadmin.quiz.store'), [
                'id_materi' => $materi->id_materi,
                'tipe_test' => 'pretest',
                'timer' => 30,
            ]);

        $response->assertRedirect(route('superadmin.quiz.index'));
        $this->assertDatabaseHas('quiz', [
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'timer' => 30,
        ]);
    }

    public function test_superadmin_soal_page_can_be_rendered(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Dasar Jaringan Komputer',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi materi',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->get(route('superadmin.soal.index', $quiz));

        $response->assertStatus(200);
        $response->assertSee('Kelola Soal Quiz');
    }

    public function test_superadmin_can_create_soal_with_pilihan_jawaban(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Dasar Jaringan Komputer',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi materi',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->post(route('superadmin.soal.store', $quiz), [
                'soals' => [
                    [
                        'pertanyaan' => 'Apa kepanjangan dari LAN?',
                        'pilihan_a' => 'Local Area Network',
                        'pilihan_b' => 'Large Area Network',
                        'pilihan_c' => 'Long Area Network',
                        'pilihan_d' => 'Low Area Network',
                        'jawaban_benar' => 'a',
                    ],
                ],
            ]);

        $response->assertRedirect(route('superadmin.soal.index', $quiz));

        $this->assertDatabaseHas('soal', [
            'id_quiz' => $quiz->id_quiz,
            'pertanyaan' => 'Apa kepanjangan dari LAN?',
            'jawaban_benar' => 'a',
            'nip_superadmin' => $this->superadmin->nip,
        ]);

        $soal = Soal::where('id_quiz', $quiz->id_quiz)->first();
        $this->assertNotNull($soal);

        $this->assertDatabaseHas('pilihan_jawaban', [
            'id_soal' => $soal->id_soal,
            'label' => 'a',
            'teks_pilihan' => 'Local Area Network',
        ]);
        $this->assertDatabaseHas('pilihan_jawaban', [
            'id_soal' => $soal->id_soal,
            'label' => 'b',
            'teks_pilihan' => 'Large Area Network',
        ]);

        // Verifikasi accessor model
        $this->assertEquals('Local Area Network', $soal->pilihan_a);
        $this->assertEquals('Large Area Network', $soal->pilihan_b);
        $this->assertEquals('Long Area Network', $soal->pilihan_c);
        $this->assertEquals('Low Area Network', $soal->pilihan_d);
    }

    public function test_superadmin_can_update_soal_with_pilihan_jawaban(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Dasar Jaringan Komputer',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi materi',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        $soal = Soal::create([
            'id_quiz' => $quiz->id_quiz,
            'pertanyaan' => 'Pertanyaan awal',
            'jawaban_benar' => 'a',
            'nip_superadmin' => $this->superadmin->nip,
        ]);

        $soal->syncPilihan([
            'a' => 'Opsi A',
            'b' => 'Opsi B',
            'c' => 'Opsi C',
            'd' => 'Opsi D',
        ]);

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->put(route('superadmin.soal.update', $soal), [
                'pertanyaan' => 'Pertanyaan revisi',
                'pilihan_a' => 'Opsi A Baru',
                'pilihan_b' => 'Opsi B Baru',
                'pilihan_c' => 'Opsi C Baru',
                'pilihan_d' => 'Opsi D Baru',
                'jawaban_benar' => 'b',
            ]);

        $response->assertRedirect(route('superadmin.soal.index', $quiz->id_quiz));

        $this->assertDatabaseHas('soal', [
            'id_soal' => $soal->id_soal,
            'pertanyaan' => 'Pertanyaan revisi',
            'jawaban_benar' => 'b',
        ]);

        $soal->refresh();
        $this->assertEquals('Opsi A Baru', $soal->pilihan_a);
        $this->assertEquals('Opsi B Baru', $soal->pilihan_b);
        $this->assertEquals('b', $soal->jawaban_benar);
    }

    public function test_superadmin_can_delete_soal(): void
    {
        $materi = Materi::create([
            'judul_materi' => 'Dasar Jaringan Komputer',
            'nip' => $this->guru->nip,
            'id_kelas' => $this->kelas->id_kelas,
            'isi_materi' => 'Deskripsi materi',
        ]);

        $quiz = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        $soal = Soal::create([
            'id_quiz' => $quiz->id_quiz,
            'pertanyaan' => 'Pertanyaan dihapus',
            'jawaban_benar' => 'a',
        ]);

        $soal->syncPilihan([
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
            'd' => 'D',
        ]);

        $response = $this->actingAs($this->superadmin, 'superadmin')
            ->delete(route('superadmin.soal.destroy', $soal));

        $response->assertRedirect(route('superadmin.soal.index', $quiz->id_quiz));
        $this->assertDatabaseMissing('soal', ['id_soal' => $soal->id_soal]);
        $this->assertDatabaseMissing('pilihan_jawaban', ['id_soal' => $soal->id_soal]);
    }
}
