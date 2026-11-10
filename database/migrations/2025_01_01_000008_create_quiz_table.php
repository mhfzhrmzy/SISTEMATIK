<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz', function (Blueprint $table) {
            $table->id('id_quiz');
            $table->unsignedBigInteger('id_materi');
            $table->enum('tipe_test', ['pretest', 'posttest']);
            $table->unsignedInteger('poin')->default(10);
            $table->unsignedInteger('timer')->default(30);
            $table->boolean('is_shuffle')->default(false);
            $table->date('tanggal');
            $table->time('jam_mulai')->nullable();
            $table->timestamps();

            $table->foreign('id_materi')->references('id_materi')->on('materi')->onDelete('cascade');
            $table->index(['id_materi', 'tipe_test'], 'idx_quiz_materi_tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz');
    }
};
