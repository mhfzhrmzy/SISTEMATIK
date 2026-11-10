<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna_siswa', function (Blueprint $table) {
            $table->string('nisn', 10)->primary();
            $table->string('nama_lengkap', 60);
            $table->unsignedBigInteger('id_kelas');
            $table->string('password');
            $table->string('foto_profile')->nullable();
            $table->string('dibuat_oleh', 18)->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_kelas')->references('id_kelas')->on('kelas');
            $table->foreign('dibuat_oleh')->references('nip')->on('super_admin')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna_siswa');
    }
};
