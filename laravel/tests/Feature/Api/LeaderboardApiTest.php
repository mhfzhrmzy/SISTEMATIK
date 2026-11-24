<?php

namespace Tests\Feature\Api;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Leaderboard;
use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LeaderboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_leaderboard_hanya_menampilkan_siswa_sekelas_dengan_peringkat_benar(): void
    {
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Komputer & Jaringan']);
        $kelas = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 1,
            'tahun' => '2026',
        ]);
        $kelasLain = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 2,
            'tahun' => '2026',
        ]);

        $siswa = $this->buatSiswa($kelas, '0051234561', 'Andi Pratama');
        $siswaDua = $this->buatSiswa($kelas, '0051234562', 'Bagus Wijaya');
        $siswaLain = $this->buatSiswa($kelasLain, '0051234563', 'Citra Lestari');

        $materi = $this->buatMateri($kelas, 'Materi Kelas Sendiri');
        $materiLain = $this->buatMateri($kelasLain, 'Materi Kelas Lain');
        $quiz = $this->buatQuiz($materi);
        $quizLain = $this->buatQuiz($materiLain);

        Leaderboard::create(['id_quiz' => $quiz->id_quiz, 'nisn' => $siswa->nisn, 'total_poin' => 100]);
        Leaderboard::create(['id_quiz' => $quiz->id_quiz, 'nisn' => $siswaDua->nisn, 'total_poin' => 50]);
        Leaderboard::create(['id_quiz' => $quizLain->id_quiz, 'nisn' => $siswaLain->nisn, 'total_poin' => 999]);

        $response = $this->withToken($this->tokenSiswa($siswa))
            ->getJson('/api/siswa/leaderboard');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'message',
                'kelas',
                'jurusan',
                'total_siswa',
                'data' => [[
                    'nisn',
                    'nama_lengkap',
                    'total_poin',
                    'peringkat',
                    'is_me',
                ]],
            ])
            ->assertJsonPath('kelas', '10 1')
            ->assertJsonPath('jurusan', 'Teknik Komputer & Jaringan')
            ->assertJsonPath('total_siswa', 2)
            ->assertJsonPath('data.0.nisn', $siswa->nisn)
            ->assertJsonPath('data.0.nama_lengkap', 'Andi Pratama')
            ->assertJsonPath('data.0.total_poin', 100)
            ->assertJsonPath('data.0.peringkat', 1)
            ->assertJsonPath('data.0.is_me', true)
            ->assertJsonPath('data.1.nisn', $siswaDua->nisn)
            ->assertJsonPath('data.1.total_poin', 50)
            ->assertJsonPath('data.1.peringkat', 2)
            ->assertJsonPath('data.1.is_me', false);

        $nisnDaftar = array_column($response->json('data'), 'nisn');
        $this->assertNotContains($siswaLain->nisn, $nisnDaftar);
    }

    public function test_leaderboard_mengabaikan_kuis_materi_tahun_lain(): void
    {
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Komputer & Jaringan']);
        $kelas = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 1,
            'tahun' => '2026',
        ]);
        $kelasTahunLain = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 1,
            'tahun' => '2025',
        ]);

        $siswa = $this->buatSiswa($kelas, '0051234561', 'Andi Pratama');

        // Quiz di materi kelas tahun 2025 → tidak ikut untuk siswa tahun 2026
        $materiTahunLain = $this->buatMateri($kelasTahunLain, 'Materi Angkatan 2025');
        $quizTahunLain = $this->buatQuiz($materiTahunLain);
        Leaderboard::create(['id_quiz' => $quizTahunLain->id_quiz, 'nisn' => $siswa->nisn, 'total_poin' => 300]);

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson('/api/siswa/leaderboard')
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('total_siswa', 0);
    }

    private function buatSiswa(Kelas $kelas, string $nisn, string $nama): PenggunaSiswa
    {
        return PenggunaSiswa::create([
            'nisn' => $nisn,
            'nama_lengkap' => $nama,
            'id_kelas' => $kelas->id_kelas,
            'password' => Hash::make('password123'),
        ]);
    }

    private function buatMateri(Kelas $kelas, string $judul): Materi
    {
        $guru = Guru::firstOrCreate(['nip' => '198712345678901234'], [
            'nama_lengkap' => 'Budi Santoso, S.Pd',
            'password' => Hash::make('password123'),
        ]);

        return Materi::create([
            'nip' => $guru->nip,
            'id_kelas' => $kelas->id_kelas,
            'judul_materi' => $judul,
            'isi_materi' => 'Isi materi '.$judul,
        ]);
    }

    private function buatQuiz(Materi $materi): Quiz
    {
        return Quiz::create([
            'id_materi' => $materi->id_materi,
            'tipe_test' => 'posttest',
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '08:00',
        ]);
    }

    private function tokenSiswa(PenggunaSiswa $siswa): string
    {
        return $siswa->createToken('tes')->plainTextToken;
    }
}
