<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusans = [
            'Teknik Alat Berat',
            'Teknik Kendaraan Ringan',
            'Teknik Sepeda Motor',
            'Teknik Pemesinan',
            'Teknik Instalasi Listrik',
            'Teknik Pembangkit Listrik',
            'Teknik Mekatronika',
            'Teknik Audio Video',
            'Teknik Komputer & Jaringan',
            'Teknik Konstruksi & Perumahan',
            'Desain Permodelan & Informasi Bangunan',
            'Desain Komunikasi Visual',
        ];

        foreach ($jurusans as $nama) {
            Jurusan::firstOrCreate(['nama_jurusan' => $nama]);
        }
    }
}
