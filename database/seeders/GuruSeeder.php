<?php

namespace Database\Seeders;

use App\Models\AdminGuru;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $gurus = [
            [
                'nip' => '198501012010011001',
                'nama_lengkap' => 'Budi Santoso, S.Pd',
                'password' => Hash::make('password123'),
            ],
            [
                'nip' => '198803152012022002',
                'nama_lengkap' => 'Siti Rahmawati, M.Kom',
                'password' => Hash::make('password123'),
            ],
            [
                'nip' => '199005202015031003',
                'nama_lengkap' => 'Ahmad Fauzi, S.T',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($gurus as $guru) {
            AdminGuru::firstOrCreate(['nip' => $guru['nip']], $guru);
        }
    }
}
