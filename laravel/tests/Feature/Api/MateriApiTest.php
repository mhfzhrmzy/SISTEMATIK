<?php

namespace Tests\Feature\Api;

use App\Models\AdminGuru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Leaderboard;
use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MateriApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_materi_hanya_materi_sekelas_dengan_struktur_benar(): void
    {
        $guru = $this->buatGuru();
        $kelas = $this->buatKelas(10, 1);
        $kelasLain = $this->buatKelas(10, 2);
        $siswa = $this->buatSiswa($kelas, '0051234561');

        // Materi dengan pretest yang belum dikerjakan → pretest_completed false
        $materiPretest = $this->buatMateri($kelas, $guru, 'Dasar Jaringan');
        Quiz::create([
            'id_materi' => $materiPretest->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);

        // Materi tanpa quiz → pretest_completed true
        $this->buatMateri($kelas, $guru, 'Sistem Operasi');

        // Materi kelas lain → tidak boleh muncul
        $this->buatMateri($kelasLain, $guru, 'Materi Kelas Lain');

        $response = $this->withToken($this->tokenSiswa($siswa))
            ->getJson('/api/siswa/materi');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'message',
                'data' => [[
                    'id_materi',
                    'judul_materi',
                    'isi_materi',
                    'kelas',
                    'jurusan',
                    'upload_file',
                    'guru_pengajar',
                    'quiz_count',
                    'pretest_count',
                    'pretest_completed',
                ]],
            ]);

        $data = collect($response->json('data'));

        $this->assertEqualsCanonicalizing(
            ['Dasar Jaringan', 'Sistem Operasi'],
            $data->pluck('judul_materi')->all()
        );

        $dasar = $data->firstWhere('judul_materi', 'Dasar Jaringan');
        $this->assertSame('10 1', $dasar['kelas']);
        $this->assertSame('Teknik Komputer & Jaringan', $dasar['jurusan']);
        $this->assertSame($guru->nama_lengkap, $dasar['guru_pengajar']);
        $this->assertSame(1, $dasar['quiz_count']);
        $this->assertSame(1, $dasar['pretest_count']);
        $this->assertFalse($dasar['pretest_completed']);

        $sistem = $data->firstWhere('judul_materi', 'Sistem Operasi');
        $this->assertSame(0, $sistem['quiz_count']);
        $this->assertSame(0, $sistem['pretest_count']);
        $this->assertTrue($sistem['pretest_completed']);
    }

    public function test_pretest_completed_true_setelah_pretest_dikerjakan(): void
    {
        $guru = $this->buatGuru();
        $kelas = $this->buatKelas(10, 1);
        $siswa = $this->buatSiswa($kelas, '0051234561');
        $materi = $this->buatMateri($kelas, $guru, 'Dasar Python');
        $pretest = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);
        Leaderboard::create([
            'id_quiz' => $pretest->id_quiz,
            'nisn' => $siswa->nisn,
            'total_poin' => 80,
        ]);

        $response = $this->withToken($this->tokenSiswa($siswa))
            ->getJson('/api/siswa/materi');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.pretest_completed', true);
    }

    public function test_detail_materi_bisa_diambil_dengan_struktur_quizzes(): void
    {
        $guru = $this->buatGuru();
        $kelas = $this->buatKelas(10, 1);
        $siswa = $this->buatSiswa($kelas, '0051234561');
        $materi = $this->buatMateri($kelas, $guru, 'Dasar Jaringan');
        Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'poin' => 10,
            'timer' => 30,
            'tanggal' => now()->toDateString(),
        ]);

        $response = $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/materi/{$materi->id_materi}");

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id_materi',
                    'judul_materi',
                    'isi_materi',
                    'upload_file',
                    'guru_pengajar',
                    'pretest_completed',
                    'quizzes' => [[
                        'id_quiz',
                        'tipe_test',
                        'poin',
                        'timer',
                        'tanggal',
                        'score',
                        'attempted',
                    ]],
                ],
            ])
            ->assertJsonPath('data.id_materi', $materi->id_materi)
            ->assertJsonPath('data.guru_pengajar', $guru->nama_lengkap)
            ->assertJsonPath('data.quizzes.0.tipe_test', 'pretest')
            ->assertJsonPath('data.quizzes.0.attempted', false);
    }

    public function test_detail_materi_ditolak_untuk_kelas_lain(): void
    {
        $guru = $this->buatGuru();
        $kelas = $this->buatKelas(10, 1);
        $kelasLain = $this->buatKelas(10, 2);
        $siswa = $this->buatSiswa($kelas, '0051234561');
        $materi = $this->buatMateri($kelasLain, $guru, 'Materi Kelas Lain');

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/materi/{$materi->id_materi}")
            ->assertForbidden()
            ->assertJsonPath('message', 'Kamu tidak memiliki akses ke materi ini.');
    }

    public function test_file_materi_ditolak_sebelum_pretest_selesai(): void
    {
        Storage::fake('public');
        $guru = $this->buatGuru();
        $kelas = $this->buatKelas(10, 1);
        $siswa = $this->buatSiswa($kelas, '0051234561');
        $materi = $this->buatMateri($kelas, $guru, 'Dasar Jaringan', [
            'upload_file' => 'materi/dasar-jaringan.pdf',
        ]);
        Storage::disk('public')->put('materi/dasar-jaringan.pdf', 'isi pdf');
        Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/materi/{$materi->id_materi}/file")
            ->assertForbidden()
            ->assertJsonPath('message', 'Kamu harus menyelesaikan Pre-Test terlebih dahulu sebelum dapat mengakses file materi.');
    }

    public function test_file_materi_bisa_diambil_setelah_pretest_selesai(): void
    {
        Storage::fake('public');
        $guru = $this->buatGuru();
        $kelas = $this->buatKelas(10, 1);
        $siswa = $this->buatSiswa($kelas, '0051234561');
        $materi = $this->buatMateri($kelas, $guru, 'Dasar Jaringan', [
            'upload_file' => 'materi/dasar-jaringan.pdf',
        ]);
        Storage::disk('public')->put('materi/dasar-jaringan.pdf', 'isi pdf');
        $pretest = Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'pretest',
            'tanggal' => now()->toDateString(),
        ]);
        Leaderboard::create([
            'id_quiz' => $pretest->id_quiz,
            'nisn' => $siswa->nisn,
            'total_poin' => 75,
        ]);

        $this->withToken($this->tokenSiswa($siswa))
            ->get("/api/siswa/materi/{$materi->id_materi}/file")
            ->assertOk();
    }

    public function test_file_materi_ditolak_untuk_kelas_lain(): void
    {
        Storage::fake('public');
        $guru = $this->buatGuru();
        $kelas = $this->buatKelas(10, 1);
        $kelasLain = $this->buatKelas(10, 2);
        $siswa = $this->buatSiswa($kelas, '0051234561');
        $materi = $this->buatMateri($kelasLain, $guru, 'Materi Kelas Lain', [
            'upload_file' => 'materi/kelas-lain.pdf',
        ]);
        Storage::disk('public')->put('materi/kelas-lain.pdf', 'isi pdf');

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/materi/{$materi->id_materi}/file")
            ->assertForbidden()
            ->assertJsonPath('message', 'Kamu tidak memiliki akses ke file materi ini.');
    }

    private function tokenSiswa(PenggunaSiswa $siswa): string
    {
        return $siswa->createToken('tes')->plainTextToken;
    }

    private function buatGuru(): AdminGuru
    {
        return AdminGuru::create([
            'nip' => '198712345678901234',
            'nama_lengkap' => 'Budi Santoso, S.Pd',
            'password' => Hash::make('password123'),
        ]);
    }

    private function buatKelas(int $tingkat, int $rombel, string $tahun = '2026'): Kelas
    {
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Komputer & Jaringan']);

        return Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => $tingkat,
            'rombel' => $rombel,
            'tahun' => $tahun,
        ]);
    }

    private function buatSiswa(Kelas $kelas, string $nisn, string $nama = 'Andi Pratama'): PenggunaSiswa
    {
        return PenggunaSiswa::create([
            'nisn' => $nisn,
            'nama_lengkap' => $nama,
            'id_kelas' => $kelas->id_kelas,
            'password' => Hash::make('password123'),
        ]);
    }

    private function buatMateri(Kelas $kelas, AdminGuru $guru, string $judul, array $atribut = []): Materi
    {
        return Materi::create(array_merge([
            'nip' => $guru->nip,
            'id_kelas' => $kelas->id_kelas,
            'judul_materi' => $judul,
            'isi_materi' => 'Isi materi '.$judul,
        ], $atribut));
    }
}
