<?php

namespace Database\Seeders;

use App\Models\AdminGuru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    public function run(): void
    {
        $guru = AdminGuru::first();

        if (! $guru) {
            return;
        }

        $jurusan = Jurusan::where('nama_jurusan', 'Teknik Komputer & Jaringan')->first();
        $kelas = $jurusan
            ? Kelas::where('id_jurusan', $jurusan->id_jurusan)
                ->where('tingkat', 10)
                ->where('rombel', 1)
                ->first()
            : null;

        $materis = [
            [
                'nip' => $guru->nip,
                'id_kelas' => $kelas?->id_kelas,
                'judul_materi' => 'Pengenalan Jaringan Komputer',
                'isi_materi' => 'Materi dasar tentang jaringan komputer, topologi, dan perangkat jaringan.',
            ],
            [
                'nip' => $guru->nip,
                'id_kelas' => $kelas?->id_kelas,
                'judul_materi' => 'Sistem Operasi Jaringan',
                'isi_materi' => 'Pengenalan sistem operasi yang digunakan di jaringan: Linux Server dan Windows Server.',
            ],
        ];

        foreach ($materis as $data) {
            Materi::firstOrCreate(
                ['judul_materi' => $data['judul_materi']],
                $data
            );
        }
    }
}
