<?php

namespace Tests\Feature;

use App\Models\AdminGuru;
use App\Models\Materi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MateriPreviewTest extends TestCase
{
    use RefreshDatabase;

    private AdminGuru $guru;

    private Materi $materi;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->guru = AdminGuru::create([
            'nip' => '198501012015121002',
            'nama_lengkap' => 'Guru Test',
            'password' => bcrypt('password123'),
        ]);

        $file = UploadedFile::fake()->create('modul_test.pdf', 100, 'application/pdf');
        $filePath = $file->store('materi', 'public');

        $this->materi = Materi::create([
            'judul_materi' => 'Materi Pemrograman Web',
            'nip' => $this->guru->nip,
            'kelas' => '10',
            'jurusan' => 'Teknik Komputer & Jaringan',
            'isi_materi' => 'Isi deskripsi materi web',
            'upload_file' => $filePath,
        ]);
    }

    public function test_guru_can_access_edit_materi_page(): void
    {
        $response = $this->actingAs($this->guru, 'admin')
            ->get(route('materi.edit', $this->materi));

        $response->assertStatus(200);
        $response->assertSee('Edit Data Materi');
        $response->assertSee('Lihat File Modul');
    }

    public function test_guru_can_view_direct_materi_file(): void
    {
        $response = $this->actingAs($this->guru, 'admin')
            ->get(route('materi.show', $this->materi));

        $response->assertStatus(200);
    }
}
