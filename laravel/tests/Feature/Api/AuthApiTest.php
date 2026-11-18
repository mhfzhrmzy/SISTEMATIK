<?php

namespace Tests\Feature\Api;

use App\Models\AdminGuru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\PenggunaSiswa;
use App\Models\SuperAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
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
                'expires_at',
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

        $kedaluwarsa = Carbon::parse($response->json('expires_at'));

        $this->assertTrue(
            $kedaluwarsa->between(now()->addDays(29), now()->addDays(31)),
            'Token siswa seharusnya berlaku sekitar 30 hari.'
        );

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

    public function test_siswa_bisa_memperbarui_nama_profil(): void
    {
        $siswa = $this->buatSiswa();
        $token = $siswa->createToken('tes')->plainTextToken;

        $this->withToken($token)
            ->putJson('/api/siswa/profil', ['nama_lengkap' => 'Andi Wijaya'])
            ->assertOk()
            ->assertJsonPath('message', 'Profil siswa berhasil diperbarui.')
            ->assertJsonPath('user.nama_lengkap', 'Andi Wijaya');

        $this->assertDatabaseHas('pengguna_siswa', [
            'nisn' => $siswa->nisn,
            'nama_lengkap' => 'Andi Wijaya',
        ]);
    }

    public function test_update_profil_menolak_nama_kosong(): void
    {
        $siswa = $this->buatSiswa();
        $token = $siswa->createToken('tes')->plainTextToken;

        $this->withToken($token)
            ->putJson('/api/siswa/profil', ['nama_lengkap' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama_lengkap']);
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

        $this->app['auth']->forgetGuards();

        $this->withToken($token)
            ->getJson('/api/siswa/profil')
            ->assertUnauthorized();
    }

    public function test_token_yang_sudah_kedaluwarsa_ditolak(): void
    {
        $siswa = $this->buatSiswa();
        $token = $siswa->createToken(
            'tes',
            ['*'],
            now()->subMinute()
        )->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/siswa/profil')
            ->assertUnauthorized();
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

    public function test_ganti_password_mencabut_token_perangkat_lain(): void
    {
        $siswa = $this->buatSiswa();
        $tokenSaatIni = $siswa->createToken('hp-ini')->plainTextToken;
        $tokenLain = $siswa->createToken('hp-lain')->plainTextToken;

        $this->withToken($tokenSaatIni)
            ->putJson('/api/siswa/profil/password', [
                'password_lama' => 'password123',
                'password_baru' => 'password-baru-baru',
                'password_baru_confirmation' => 'password-baru-baru',
            ])
            ->assertOk();

        $this->app['auth']->forgetGuards();

        $this->withToken($tokenSaatIni)
            ->getJson('/api/siswa/profil')
            ->assertOk();

        $this->app['auth']->forgetGuards();

        $this->withToken($tokenLain)
            ->getJson('/api/siswa/profil')
            ->assertUnauthorized();

        $this->assertDatabaseCount('personal_access_tokens', 1);
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
