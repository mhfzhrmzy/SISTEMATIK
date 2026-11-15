<?php

namespace Tests\Feature\Api;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\PenggunaSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_bisa_login_dan_mendapat_token(): void
    {
        $siswa = $this->buatSiswa();

        $response = $this->postJson('/api/login/siswa', [
            'nisn' => $siswa->nisn,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('role', 'siswa')
            ->assertJsonPath('user.nisn', $siswa->nisn)
            ->assertJsonPath('user.nama_lengkap', 'Andi Pratama')
            ->assertJsonPath('user.kelas', '10 1')
            ->assertJsonPath('user.jurusan', 'Teknik Komputer & Jaringan')
            ->assertJsonPath('user.tahun', '2026')
            ->assertJsonPath('user.poin', 0)
            ->assertJsonStructure([
                'message',
                'token',
                'role',
                'user' => [
                    'nisn',
                    'nama_lengkap',
                    'id_kelas',
                    'kelas',
                    'jurusan',
                    'tahun',
                    'poin',
                    'foto_profile',
                ],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_menolak_nisn_yang_bukan_10_digit(): void
    {
        $this->postJson('/api/login/siswa', [
            'nisn' => '12345',
            'password' => 'password123',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'NISN harus terdiri dari 10 digit.')
            ->assertJsonValidationErrors(['nisn']);
    }

    public function test_login_menolak_password_salah(): void
    {
        $siswa = $this->buatSiswa();

        $this->postJson('/api/login/siswa', [
            'nisn' => $siswa->nisn,
            'password' => 'password-salah',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'NISN atau password salah.');
    }

    public function test_profil_siswa_bisa_diambil_dengan_token_sanctum(): void
    {
        $siswa = $this->buatSiswa();
        $token = $siswa->createToken('tes')->plainTextToken;

        $this->assertSame(
            $siswa->nisn,
            (string) $siswa->tokens()->first()->tokenable_id
        );

        $this->withToken($token)
            ->getJson('/api/siswa/profil')
            ->assertOk()
            ->assertJsonPath('user.nisn', $siswa->nisn)
            ->assertJsonPath('user.kelas', '10 1')
            ->assertJsonPath('user.jurusan', 'Teknik Komputer & Jaringan');
    }

    private function buatSiswa(): PenggunaSiswa
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer & Jaringan',
        ]);

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
}
