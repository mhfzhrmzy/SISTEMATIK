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
            $table->unsignedBigInteger('id_quiz');
            $table->string('nisn', 10);
            $table->unsignedInteger('total_poin')->default(0);
            $table->timestamps();

            $table->unique(['id_quiz', 'nisn']);
            $table->foreign('id_quiz')->references('id_quiz')->on('quiz')->onDelete('cascade');
            $table->foreign('nisn')->references('nisn')->on('pengguna_siswa')->onDelete('cascade');
            $table->index(['id_quiz', 'total_poin'], 'idx_leaderboard_quiz_poin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard');
    }
};
