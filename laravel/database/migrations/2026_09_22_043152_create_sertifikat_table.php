<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id('id_sertifikat');

            // Relasi ke pengguna_siswa (nisn bertipe string)
            $table->string('nisn', 10);
            $table->foreign('nisn')->references('nisn')->on('pengguna_siswa')->onDelete('cascade');

            // Relasi opsional jika diterbitkan guru (nip & id_materi)
            $table->string('nip', 18)->nullable();
            $table->foreign('nip')->references('nip')->on('admin_guru')->onDelete('set null');

            $table->unsignedBigInteger('id_materi')->nullable();
            $table->foreign('id_materi')->references('id_materi')->on('materi')->onDelete('set null');

            // Data Sertifikat
            $table->string('judul_sertifikat');
            $table->string('penerbit')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_sertifikat');
            $table->enum('tipe_sertifikat', ['mandiri', 'materi'])->default('mandiri');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
