<?php

namespace Tests\Feature\Api;

use App\Models\AdminGuru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Sertifikat;
use App\Models\KategoriSertifikat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SertifikatApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_sertifikat_siswa_dengan_kategori_string_dan_guru(): void
    {
        Storage::fake('public');
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Komputer & Jaringan']);
        $kelas = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 1,
            'tahun' => '2026',
        ]);
        $siswa = $this->buatSiswa($kelas, '0051234561', 'Andi Pratama');
        $siswaLain = $this->buatSiswa($kelas, '0051234562', 'Bagus Wijaya');

        $guru = AdminGuru::create([
            'nip' => '198712345678901234',
            'nama_lengkap' => 'Budi Santoso, S.Pd',
            'password' => Hash::make('password123'),
        ]);
        $materi = Materi::create([
            'nip' => $guru->nip,
            'id_kelas' => $kelas->id_kelas,
            'judul_materi' => 'Dasar Jaringan',
            'isi_materi' => 'Isi materi.',
        ]);
        $kategori = KategoriSertifikat::create(['nama_kategori' => 'Pelatihan']);

        Sertifikat::create([
            'nisn' => $siswa->nisn,
            'id_materi' => $materi->id_materi,
            'judul_sertifikat' => 'Sertifikat Dasar Jaringan',
            'id_kategori' => $kategori->id_kategori,
            'penerbit' => 'SMKN 2 Jember',
            'deskripsi' => 'Sertifikat penyelesaian materi.',
            'file_sertifikat' => 'sertifikat/andi-jaringan.pdf',
            'tipe_sertifikat' => 'materi',
            'terverifikasi' => true,
            'tanggal_terbit' => '2026-07-01',
        ]);
        // Sertifikat milik siswa lain → tidak ikut
        Sertifikat::create([
            'nisn' => $siswaLain->nisn,
            'judul_sertifikat' => 'Sertifikat Bagus',
            'file_sertifikat' => 'sertifikat/bagus.pdf',
            'tipe_sertifikat' => 'mandiri',
            'tanggal_terbit' => '2026-07-02',
        ]);

        $response = $this->withToken($this->tokenSiswa($siswa))
            ->getJson('/api/siswa/sertifikat');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'message',
                'data' => [[
                    'id_sertifikat',
                    'judul_sertifikat',
                    'kategori',
                    'penerbit',
                    'deskripsi',
                    'tanggal_terbit',
                    'tipe_sertifikat',
                    'terverifikasi',
                    'file_url',
                    'badge_url',
                    'guru',
                    'materi',
                ]],
            ])
            ->assertJsonPath('data.0.judul_sertifikat', 'Sertifikat Dasar Jaringan')
            ->assertJsonPath('data.0.kategori', 'Pelatihan')
            ->assertJsonPath('data.0.tipe_sertifikat', 'materi')
            ->assertJsonPath('data.0.terverifikasi', true)
            ->assertJsonPath('data.0.file_url', Storage::disk('public')->url('sertifikat/andi-jaringan.pdf'))
            ->assertJsonPath('data.0.guru.nip', $guru->nip)
            ->assertJsonPath('data.0.guru.nama_lengkap', $guru->nama_lengkap)
            ->assertJsonPath('data.0.materi.id_materi', $materi->id_materi)
            ->assertJsonPath('data.0.materi.judul_materi', 'Dasar Jaringan');
    }

    public function test_sertifikat_tanpa_materi_memiliki_guru_null(): void
    {
        Storage::fake('public');
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Komputer & Jaringan']);
        $kelas = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 1,
            'tahun' => '2026',
        ]);
        $siswa = $this->buatSiswa($kelas, '0051234561', 'Andi Pratama');

        Sertifikat::create([
            'nisn' => $siswa->nisn,
            'judul_sertifikat' => 'Sertifikat Mandiri',
            'file_sertifikat' => 'sertifikat/mandiri.pdf',
            'tipe_sertifikat' => 'mandiri',
            'tanggal_terbit' => '2026-07-01',
        ]);

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson('/api/siswa/sertifikat')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.kategori', null)
            ->assertJsonPath('data.0.guru', null)
            ->assertJsonPath('data.0.materi', null);
    }

    public function test_download_sertifikat_milik_sendiri_berhasil(): void
    {
        Storage::fake('public');
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Komputer & Jaringan']);
        $kelas = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 1,
            'tahun' => '2026',
        ]);
        $siswa = $this->buatSiswa($kelas, '0051234561', 'Andi Pratama');
        $sertifikat = Sertifikat::create([
            'nisn' => $siswa->nisn,
            'judul_sertifikat' => 'Sertifikat Andi',
            'file_sertifikat' => 'sertifikat/andi.pdf',
            'tipe_sertifikat' => 'mandiri',
            'tanggal_terbit' => '2026-07-01',
        ]);
        Storage::disk('public')->put('sertifikat/andi.pdf', 'isi pdf sertifikat');

        $this->withToken($this->tokenSiswa($siswa))
            ->get("/api/siswa/sertifikat/{$sertifikat->id_sertifikat}/download")
            ->assertOk();
    }

    public function test_download_sertifikat_orang_lain_ditolak(): void
    {
        Storage::fake('public');
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Komputer & Jaringan']);
        $kelas = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 1,
            'tahun' => '2026',
        ]);
        $siswa = $this->buatSiswa($kelas, '0051234561', 'Andi Pratama');
        $siswaLain = $this->buatSiswa($kelas, '0051234562', 'Bagus Wijaya');
        $sertifikat = Sertifikat::create([
            'nisn' => $siswaLain->nisn,
            'judul_sertifikat' => 'Sertifikat Bagus',
            'file_sertifikat' => 'sertifikat/bagus.pdf',
            'tipe_sertifikat' => 'mandiri',
            'tanggal_terbit' => '2026-07-01',
        ]);
        Storage::disk('public')->put('sertifikat/bagus.pdf', 'isi pdf');

        $this->withToken($this->tokenSiswa($siswa))
            ->getJson("/api/siswa/sertifikat/{$sertifikat->id_sertifikat}/download")
            ->assertNotFound()
            ->assertJsonPath('message', 'Anda tidak memiliki akses ke sertifikat ini.');
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

    private function tokenSiswa(PenggunaSiswa $siswa): string
    {
        return $siswa->createToken('tes')->plainTextToken;
    }
}
