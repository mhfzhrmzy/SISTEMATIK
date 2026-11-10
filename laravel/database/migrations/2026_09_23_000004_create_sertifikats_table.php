<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikats', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 20)->index(); // tanpa foreign key, cukup diindex

            $table->string('judul');
            $table->string('kategori');
            $table->string('penerbit');
            $table->string('deskripsi')->nullable();
            $table->unsignedInteger('jam_pelatihan')->nullable();
            $table->string('event_kompetisi')->nullable();
            $table->string('file_path')->nullable();
            $table->string('badge_url')->nullable();
            $table->boolean('terverifikasi')->default(false);
            $table->date('tanggal_terbit');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikats');
    }
};
