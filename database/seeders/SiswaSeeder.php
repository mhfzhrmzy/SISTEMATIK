<?php

namespace Database\Seeders;

use App\Models\PenggunaSiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $siswas = [
            [
                'nisn' => '0051234561',
                'nama_lengkap' => 'Andi Pratama',
                'kelas' => '10',
                'jurusan' => 'Teknik Komputer & Jaringan',
                'password' => Hash::make('password123'),
                'poin' => 0,
            ],
            [
                'nisn' => '0051234571',
                'nama_lengkap' => 'Andi sultan',
                'kelas' => '10',
                'jurusan' => 'Teknik Komputer & Jaringan',
                'password' => Hash::make('password123'),
                'poin' => 0,
            ],
            [
                'nisn' => '0051234562',
                'nama_lengkap' => 'Dewi Lestari',
                'kelas' => '11',
                'jurusan' => 'Desain Komunikasi Visual',
                'password' => Hash::make('password123'),
                'poin' => 0,
            ],
            [
                'nisn' => '0051234563',
                'nama_lengkap' => 'Rizky Febrian',
                'kelas' => '12',
                'jurusan' => 'Teknik Pemesinan',
                'password' => Hash::make('password123'),
                'poin' => 0,
            ],
            [
                'nisn' => '0051234564',
                'nama_lengkap' => 'Siti Nurhaliza',
                'kelas' => '10',
                'jurusan' => 'Teknik Audio Video',
                'password' => Hash::make('password123'),
                'poin' => 0,
            ],
            [
                'nisn' => '0051234565',
                'nama_lengkap' => 'Bagus Setiawan',
                'kelas' => '11',
                'jurusan' => 'Teknik Kendaraan Ringan',
                'password' => Hash::make('password123'),
                'poin' => 0,
            ],
        ];

        foreach ($siswas as $siswa) {
            PenggunaSiswa::firstOrCreate(['nisn' => $siswa['nisn']], $siswa);
        }
    }
}
