<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\PenggunaSiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $siswas = [
            ['nisn' => '0051234561', 'nama_lengkap' => 'Andi Pratama',    'jurusan' => 'Teknik Komputer & Jaringan', 'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['nisn' => '0051234571', 'nama_lengkap' => 'Andi sultan',     'jurusan' => 'Teknik Komputer & Jaringan', 'tingkat' => 10, 'rombel' => 2, 'tahun' => '2026'],
            ['nisn' => '0051234562', 'nama_lengkap' => 'Dewi Lestari',    'jurusan' => 'Desain Komunikasi Visual',   'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['nisn' => '0051234563', 'nama_lengkap' => 'Rizky Febrian',   'jurusan' => 'Teknik Pemesinan',           'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['nisn' => '0051234564', 'nama_lengkap' => 'Siti Nurhaliza',  'jurusan' => 'Teknik Audio Video',         'tingkat' => 10, 'rombel' => 1, 'tahun' => '2026'],
            ['nisn' => '0051234565', 'nama_lengkap' => 'Bagus Setiawan', 'jurusan' => 'Teknik Kendaraan Ringan',    'tingkat' => 10, 'rombel' => 2, 'tahun' => '2026'],
        ];

        foreach ($siswas as $data) {
            $jurusan = Jurusan::where('nama_jurusan', $data['jurusan'])->firstOrFail();
            $kelas = Kelas::where('id_jurusan', $jurusan->id_jurusan)
                ->where('tingkat', $data['tingkat'])
                ->where('rombel', $data['rombel'])
                ->where('tahun', $data['tahun'])
                ->firstOrFail();

            PenggunaSiswa::firstOrCreate(
                ['nisn' => $data['nisn']],
                [
                    'nama_lengkap' => $data['nama_lengkap'],
                    'id_kelas' => $kelas->id_kelas,
                    'password' => Hash::make('password123'),
                ]
            );
        }
    }
}
