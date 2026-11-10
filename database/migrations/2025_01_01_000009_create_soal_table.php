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
            $table->unsignedBigInteger('id_quiz');
            $table->string('nip_superadmin', 18)->nullable();
            $table->text('pertanyaan');
            $table->enum('jawaban_benar', ['a', 'b', 'c', 'd']);
            $table->timestamps();

            $table->foreign('id_quiz')->references('id_quiz')->on('quiz')->onDelete('cascade');
            $table->foreign('nip_superadmin')->references('nip')->on('super_admin')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
