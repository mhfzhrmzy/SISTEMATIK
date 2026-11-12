<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilihan_jawaban', function (Blueprint $table) {
            $table->id('id_pilihan');
            $table->unsignedBigInteger('id_soal');
            $table->enum('label', ['a', 'b', 'c', 'd']);
            $table->string('teks_pilihan', 500);
            $table->timestamps();

            $table->unique(['id_soal', 'label'], 'pilihan_jawaban_id_soal_label_unique');
            $table->foreign('id_soal')->references('id_soal')->on('soal')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilihan_jawaban');
    }
};
