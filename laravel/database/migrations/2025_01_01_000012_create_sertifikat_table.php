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
            $table->string('nisn', 10);
            $table->unsignedBigInteger('id_materi')->nullable();
            $table->string('judul_sertifikat');
            $table->unsignedInteger('id_kategori')->nullable();
            $table->string('penerbit')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('jam_pelatihan')->nullable();
            $table->string('event_kompetisi')->nullable();
            $table->string('file_sertifikat');
            $table->string('file_hash', 64)->nullable()->unique();
            $table->string('badge_url')->nullable();
            $table->boolean('terverifikasi')->default(false);
            $table->enum('tipe_sertifikat', ['mandiri', 'materi'])->default('mandiri');
            $table->timestamps();

            $table->foreign('nisn')->references('nisn')->on('pengguna_siswa')->onDelete('cascade');
            $table->foreign('id_materi')->references('id_materi')->on('materi')->onDelete('set null');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_sertifikat')->onDelete('set null');
            $table->index(['nisn', 'tanggal_terbit'], 'idx_sertifikat_nisn_terbit');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
