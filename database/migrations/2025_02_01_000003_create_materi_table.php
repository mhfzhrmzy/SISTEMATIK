<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi', function (Blueprint $table) {
            $table->id('id_materi');
            $table->string('nip', 18); // FK -> admin_guru.nip
            $table->string('judul_materi');
            $table->text('isi_materi');
            $table->string('upload_file')->nullable();
            $table->timestamps();

            $table->foreign('nip')->references('nip')->on('admin_guru')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
