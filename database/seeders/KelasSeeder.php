<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Buat kelas sampel yang akan dipakai oleh SiswaSeeder dan MateriSeeder.
     * Mapping mengikuti data siswa seeder: jurusan → tingkat + rombel + tahun.
     */
    public function run(): void
    {
        $kelasData = [
            ['jurusan' => 'Teknik Alat Berat',                      'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Kendaraan Ringan',                 'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Kendaraan Ringan',                 'tingkat' => 10, 'rombel' => 2, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Sepeda Motor',                     'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Pemesinan',                        'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Instalasi Listrik',                'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Pembangkit Listrik',               'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Mekatronika',                      'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Audio Video',                      'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Komputer & Jaringan',              'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Komputer & Jaringan',              'tingkat' => 10, 'rombel' => 2, 'tahun' => '2026'],
            ['jurusan' => 'Teknik Konstruksi & Perumahan',           'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Desain Permodelan & Informasi Bangunan',  'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['jurusan' => 'Desain Komunikasi Visual',                'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
        ];

        foreach ($kelasData as $data) {
            $jurusan = Jurusan::where('nama_jurusan', $data['jurusan'])->firstOrFail();

            Kelas::firstOrCreate([
                'id_jurusan' => $jurusan->id_jurusan,
                'tingkat' => $data['tingkat'],
                'rombel' => $data['rombel'],
                'tahun' => $data['tahun'],
            ]);
        }
    }
}
