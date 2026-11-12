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
            $table->string('nip', 18);
            $table->string('nip_superadmin', 18)->nullable();
            $table->unsignedBigInteger('id_kelas')->nullable();
            $table->string('judul_materi');
            $table->text('isi_materi');
            $table->string('upload_file')->nullable();
            $table->timestamps();

            $table->foreign('nip')->references('nip')->on('admin_guru')->onDelete('cascade');
            $table->foreign('nip_superadmin')->references('nip')->on('super_admin')->onDelete('set null');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('set null');
            $table->index(['id_kelas', 'created_at'], 'idx_materi_kelas_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
