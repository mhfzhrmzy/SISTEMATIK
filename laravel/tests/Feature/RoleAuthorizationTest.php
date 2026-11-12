<?php

namespace Tests\Feature;

use App\Models\AdminGuru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\PenggunaSiswa;
use App\Models\SuperAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private SuperAdmin $superadmin;

    private AdminGuru $guru;

    private PenggunaSiswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = SuperAdmin::create([
            'nip' => '199001012020121001',
            'nama_lengkap' => 'Super Admin Test',
            'password' => bcrypt('password123'),
        ]);

        $this->guru = AdminGuru::create([
            'nip' => '198501012015121002',
            'nama_lengkap' => 'Guru Test',
            'password' => bcrypt('password123'),
        ]);

        $jurusan = Jurusan::create(['nama_jurusan' => 'Teknik Komputer & Jaringan']);
        $kelas = Kelas::create([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => 10,
            'rombel' => 1,
            'tahun' => '2026',
        ]);

        $this->siswa = PenggunaSiswa::create([
            'nisn' => '0012345678',
            'nama_lengkap' => 'Siswa Test',
            'id_kelas' => $kelas->id_kelas,
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_superadmin_can_access_superadmin_page(): void
    {
        $this->actingAs($this->superadmin, 'superadmin')
            ->get(route('superadmin.index'))
            ->assertStatus(200);
    }

    public function test_guru_cannot_access_superadmin_page(): void
    {
        $this->actingAs($this->guru, 'admin')
            ->get(route('superadmin.index'))
            ->assertStatus(403);
    }

    public function test_siswa_cannot_access_superadmin_page(): void
    {
        $this->actingAs($this->siswa, 'siswa')
            ->get(route('superadmin.index'))
            ->assertStatus(403);
    }

    public function test_guru_can_access_guru_page(): void
    {
        $this->actingAs($this->guru, 'admin')
            ->get(route('materi.index'))
            ->assertStatus(200);
    }

    public function test_superadmin_cannot_access_guru_page(): void
    {
        $this->actingAs($this->superadmin, 'superadmin')
            ->get(route('materi.index'))
            ->assertStatus(403);
    }

    public function test_siswa_cannot_access_guru_page(): void
    {
        $this->actingAs($this->siswa, 'siswa')
            ->get(route('materi.index'))
            ->assertStatus(403);
    }

    public function test_siswa_can_access_siswa_page(): void
    {
        $this->actingAs($this->siswa, 'siswa')
            ->get(route('portal.materi.index'))
            ->assertStatus(200);
    }

    public function test_superadmin_cannot_access_siswa_page(): void
    {
        $this->actingAs($this->superadmin, 'superadmin')
            ->get(route('portal.materi.index'))
            ->assertStatus(403);
    }

    public function test_guru_cannot_access_siswa_page(): void
    {
        $this->actingAs($this->guru, 'admin')
            ->get(route('portal.materi.index'))
            ->assertStatus(403);
    }

    public function test_unauthenticated_user_redirection(): void
    {
        $this->get(route('superadmin.index'))
            ->assertRedirect(route('admin.login'));

        $this->get(route('materi.index'))
            ->assertRedirect(route('admin.login'));

        $this->get(route('portal.materi.index'))
            ->assertRedirect(route('siswa.login'));
    }
}
