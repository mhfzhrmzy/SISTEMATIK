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
        Schema::table('pengguna_siswa', function (Blueprint $table) {
            if (! Schema::hasColumn('pengguna_siswa', 'foto_profile')) {
                $table->string('foto_profile')->nullable()->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna_siswa', function (Blueprint $table) {
            if (Schema::hasColumn('pengguna_siswa', 'foto_profile')) {
                $table->dropColumn('foto_profile');
            }
        });
    }
};
