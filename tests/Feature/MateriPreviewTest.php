<?php

namespace Tests\Feature;

use App\Models\AdminGuru;
use App\Models\Kelas;
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

        $kelas = Kelas::findOrCreateByNama('Teknik Komputer & Jaringan', 10, 1, (string) date('Y'));

        $file = UploadedFile::fake()->create('modul_test.pdf', 100, 'application/pdf');
        $filePath = $file->store('materi', 'public');

        $this->materi = Materi::create([
            'judul_materi' => 'Materi Pemrograman Web',
            'nip' => $this->guru->nip,
            'id_kelas' => $kelas->id_kelas,
            'isi_materi' => 'Isi deskripsi materi web',
            'upload_file' => $filePath,
        ]);
    }

    public function test_guru_can_access_edit_materi_page(): void
    {
        $response = $this->actingAs($this->guru, 'admin')
            ->get(route('materi.edit', $this->materi));

        $response->assertStatus(200);
        $response->assertSee('Edit Bahan Ajar');
        $response->assertSee('Buka File');
    }

    public function test_guru_can_view_direct_materi_file(): void
    {
        $response = $this->actingAs($this->guru, 'admin')
            ->get(route('materi.show', $this->materi));

        $response->assertStatus(200);
    }
}
