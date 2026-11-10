<?php

namespace Tests\Feature;

use App\Models\AdminGuru;
use App\Models\PenggunaSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_register_screen_is_not_accessible(): void
    {
        $response = $this->get('/register/guru');

        $response->assertStatus(404);
    }

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login/guru');

        $response->assertStatus(200);
        $response->assertSee('Login Guru');
    }

    public function test_admin_can_login(): void
    {
        $admin = AdminGuru::create([
            'nip' => '198501012010011002',
            'nama_lengkap' => 'Guru Ani',
            'email' => 'ani@sekolah.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        $response = $this->post('/login/guru', [
            'nip' => '198501012010011002',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin->fresh(), 'admin');
        $response->assertRedirect('/materi');
    }

    public function test_admin_can_logout(): void
    {
        $admin = AdminGuru::create([
            'nip' => '198501012010011003',
            'nama_lengkap' => 'Guru Candra',
            'email' => 'candra@sekolah.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->post('/logout/guru');

        $this->assertGuest('admin');
        $response->assertRedirect(route('siswa.login'));
    }

    public function test_siswa_register_screen_is_not_accessible(): void
    {
        $response = $this->get('/register/siswa');

        $response->assertStatus(404);
    }

    public function test_siswa_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login/siswa');

        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    public function test_siswa_can_login(): void
    {
        $siswa = PenggunaSiswa::create([
            'nisn' => '0051234568',
            'nama_lengkap' => 'Siswa Eka',
            'email' => 'eka@sekolah.sch.id',
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
            'password' => Hash::make('password123'),
            'poin' => 0,
        ]);

        $response = $this->post('/login/siswa', [
            'nisn' => '0051234568',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($siswa->fresh(), 'siswa');
        $response->assertRedirect('/portal/materi');
    }

    public function test_siswa_can_logout(): void
    {
        $siswa = PenggunaSiswa::create([
            'nisn' => '0051234569',
            'nama_lengkap' => 'Siswa Fajar',
            'email' => 'fajar@sekolah.sch.id',
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
            'password' => Hash::make('password123'),
            'poin' => 0,
        ]);

        $this->actingAs($siswa, 'siswa');

        $response = $this->post('/logout/siswa');

        $this->assertGuest('siswa');
        $response->assertRedirect(route('siswa.login'));
    }
}
