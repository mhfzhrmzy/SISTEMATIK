<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Drop foreign key di materi yang mereferensi guru.nip
        Schema::table('materi', function (Blueprint $table) {
            $table->dropForeign(['nip']);
        });

        // Step 2: Tambah kolom guru_id di materi
        Schema::table('materi', function (Blueprint $table) {
            $table->unsignedBigInteger('guru_id')->nullable()->after('id_materi');
        });

        // Step 3: Drop dan recreate tabel guru dengan struktur baru
        Schema::dropIfExists('guru');

        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 18)->nullable()->unique();
            $table->string('nik', 16)->nullable()->unique();
            $table->string('nama_lengkap', 60);
            $table->string('password');
            $table->string('foto_profile')->nullable();
            $table->string('dibuat_oleh', 18)->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('dibuat_oleh')->references('nip')->on('super_admin')->onDelete('set null');
        });

        // Step 4: Tambah foreign key guru_id di materi
        Schema::table('materi', function (Blueprint $table) {
            $table->foreign('guru_id')->references('id')->on('guru')->onDelete('cascade');
        });

        // Step 5: Drop kolom nip lama di materi (setelah migrasi data manual jika diperlukan)
        // Commented out - jalankan manual setelah migrasi data
        // Schema::table('materi', function (Blueprint $table) {
        //     $table->dropColumn('nip');
        // });
    }

    public function down(): void
    {
        // Restore kolom nip di materi jika belum ada
        Schema::table('materi', function (Blueprint $table) {
            if (!Schema::hasColumn('materi', 'nip')) {
                $table->string('nip', 18)->nullable()->after('id_materi');
            }
        });

        // Drop foreign key dan kolom guru_id
        Schema::table('materi', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropColumn('guru_id');
        });

        // Recreate guru table dengan struktur lama
        Schema::dropIfExists('guru');

        Schema::create('guru', function (Blueprint $table) {
            $table->string('nip', 18)->primary();
            $table->string('nama_lengkap', 60);
            $table->string('password');
            $table->string('foto_profile')->nullable();
            $table->string('dibuat_oleh', 18)->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('dibuat_oleh')->references('nip')->on('super_admin')->onDelete('set null');
        });

        // Restore foreign key di materi
        Schema::table('materi', function (Blueprint $table) {
            $table->foreign('nip')->references('nip')->on('guru')->onDelete('cascade');
        });
    }
};
