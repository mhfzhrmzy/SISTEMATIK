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
            $table->string('nip', 18);        // FK -> admin_guru.nip (pembuat quiz)
            $table->unsignedBigInteger('id_materi');  // FK -> materi.id_materi
            $table->enum('tipe_test', ['pretest', 'posttest']);
            $table->unsignedInteger('poin')->default(10);  // poin per soal benar
            $table->unsignedInteger('timer')->default(30); // durasi pengerjaan (menit)
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('nip')->references('nip')->on('admin_guru')->onDelete('cascade');
            $table->foreign('id_materi')->references('id_materi')->on('materi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz');
    }
};
