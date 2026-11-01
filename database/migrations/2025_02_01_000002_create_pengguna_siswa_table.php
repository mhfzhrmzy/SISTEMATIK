<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna_siswa', function (Blueprint $table) {
            $table->string('nisn', 10)->primary(); // 10 digit, diisi manual
            $table->string('nama_lengkap', 60);
            $table->enum('jurusan', [
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
            ]);
            $table->enum('kelas', ['10 1', '10 2', '11 1', '11 2', '12 1', '12 2']);
            $table->string('tahun', 4);
            $table->string('password');
            $table->string('foto_profile')->nullable();
            $table->unsignedInteger('poin')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna_siswa');
    }
};
