<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materi_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 10);
            $table->unsignedBigInteger('id_materi');
            $table->boolean('is_dibuka')->default(false);
            $table->boolean('is_selesai')->default(false);
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();

            $table->foreign('nisn')->references('nisn')->on('pengguna_siswa')->onDelete('cascade');
            $table->foreign('id_materi')->references('id_materi')->on('materi')->onDelete('cascade');
            $table->unique(['nisn', 'id_materi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materi_siswa');
    }
};
