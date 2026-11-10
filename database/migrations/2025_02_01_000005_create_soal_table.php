<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal', function (Blueprint $table) {
            $table->id('id_soal');
            $table->unsignedBigInteger('id_quiz'); // FK -> quiz.id_quiz
            $table->text('pertanyaan');
            $table->string('pilihan_a', 500);
            $table->string('pilihan_b', 500);
            $table->string('pilihan_c', 500);
            $table->string('pilihan_d', 500);
            $table->enum('jawaban_benar', ['a', 'b', 'c', 'd']);
            $table->timestamps();

            $table->foreign('id_quiz')->references('id_quiz')->on('quiz')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
