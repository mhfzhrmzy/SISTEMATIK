<?php

namespace Tests\Feature\Api;

use App\Models\AdminGuru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\PenggunaSiswa;
use App\Models\SuperAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function test_siswa_bisa_logout_dan_token_dihapus(): void
    {
        $siswa = $this->buatSiswa();
        $token = $siswa->createToken('tes')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/siswa/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logout berhasil.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_update_password_siswa_berhasil(): void
    {
        $siswa = $this->buatSiswa();
        $token = $siswa->createToken('tes')->plainTextToken;

        $this->withToken($token)
            ->putJson('/api/siswa/profil/password', [
                'password_lama' => 'password123',
                'password_baru' => 'password-baru-baru',
                'password_baru_confirmation' => 'password-baru-baru',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Password berhasil diubah.');

        $this->assertTrue(Hash::check('password-baru-baru', $siswa->fresh()->password));
    }

    public function test_update_password_siswa_menolak_password_lama_salah(): void
    {
        $siswa = $this->buatSiswa();
        $token = $siswa->createToken('tes')->plainTextToken;

        $this->withToken($token)
            ->putJson('/api/siswa/profil/password', [
                'password_lama' => 'salah',
                'password_baru' => 'password-baru-baru',
                'password_baru_confirmation' => 'password-baru-baru',
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Password lama tidak cocok.');
    }

    public function test_update_foto_siswa_berhasil(): void
    {
        Storage::fake('public');
        $siswa = $this->buatSiswa();
        $token = $siswa->createToken('tes')->plainTextToken;
        $foto = UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg');

        $response = $this->withToken($token)
            ->post('/api/siswa/profil/foto', ['foto' => $foto]);

        $response->assertOk()
            ->assertJsonPath('message', 'Foto profil berhasil diperbarui.');

        $this->assertNotNull($siswa->fresh()->foto_profile);
        Storage::disk('public')->assertExists($siswa->fresh()->foto_profile);
    }

    public function test_login_guru_dengan_nip(): void
    {
        $guru = AdminGuru::create([
            'nip' => '198712345678901234',
            'nama_lengkap' => 'Budi Santoso, S.Pd',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson('/api/login/guru', [
            'nip' => $guru->nip,
            'password' => 'password123',
        ])
            ->assertOk()
            ->assertJsonPath('role', 'guru')
            ->assertJsonPath('user.nip', $guru->nip)
            ->assertJsonPath('user.nama_lengkap', 'Budi Santoso, S.Pd');
    }

    public function test_login_superadmin_dengan_nip(): void
    {
        $superadmin = SuperAdmin::create([
            'nip' => '197012345678901234',
            'nama_lengkap' => 'Kepala Sekolah',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson('/api/login/guru', [
            'nip' => $superadmin->nip,
            'password' => 'password123',
        ])
            ->assertOk()
            ->assertJsonPath('role', 'superadmin')
            ->assertJsonPath('user.nip', $superadmin->nip);
    }

    public function test_login_guru_menolak_nip_salah(): void
    {
        $this->postJson('/api/login/guru', [
            'nip' => '198712345678901234',
            'password' => 'password-salah',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'NIP atau password salah.');
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
