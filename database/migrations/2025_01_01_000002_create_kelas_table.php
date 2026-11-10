<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id('id_kelas');
            $table->unsignedTinyInteger('id_jurusan');
            $table->unsignedTinyInteger('tingkat')->comment('10, 11, atau 12');
            $table->unsignedTinyInteger('rombel')->comment('nomor rombongan belajar: 1, 2, ...');
            $table->string('tahun', 4);
            $table->timestamps();

            $table->unique(['id_jurusan', 'tingkat', 'rombel', 'tahun'], 'kelas_jurusan_tingkat_rombel_tahun_unique');
            $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
