<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::firstOrCreate(
            ['nip' => '123456789012345678'],
            [
                'nama_lengkap' => 'SuperAdministrator',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
