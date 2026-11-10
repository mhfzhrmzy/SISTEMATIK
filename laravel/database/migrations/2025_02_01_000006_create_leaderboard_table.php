<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard', function (Blueprint $table) {
            $table->id('id_leaderboard');
            $table->unsignedBigInteger('id_quiz'); // FK -> quiz.id_quiz
            $table->string('nisn', 10);     // FK -> pengguna_siswa.nisn
            $table->unsignedInteger('total_poin')->default(0);
            $table->unsignedInteger('peringkat')->nullable();
            $table->timestamps();

            $table->foreign('id_quiz')->references('id_quiz')->on('quiz')->onDelete('cascade');
            $table->foreign('nisn')->references('nisn')->on('pengguna_siswa')->onDelete('cascade');
            $table->unique(['id_quiz', 'nisn']); // 1 siswa = 1 baris leaderboard per quiz
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard');
    }
};
