<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->enum('kelas', ['10 1', '10 2', '11 1', '11 2', '12 1', '12 2'])->nullable()->after('nip');
            $table->enum('jurusan', [
                'Teknik Alat Berat',
                'Teknik Kendaraan Ringan',
                'Teknik Sepeda Motor',
                'Teknik Pemesinan',
                'Teknik Instalasi Listrik',
                'Teknik Pembangkit Listrik',
                'Teknik Mekatronika',
                'Teknik Audio Video',
                'Teknik Komputer & Jaringan',
                'Teknik Konstruksi & Perumahan',
                'Desain Permodelan & Informasi Bangunan',
                'Desain Komunikasi Visual',
            ])->nullable()->after('kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'jurusan']);
        });
    }
};
