<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus kolom timer_per_soal dari tabel soal.
     * Timer per soal dihapus karena bentrok dengan jadwal penjadwalan post-test;
     * satu-satunya timer yang berlaku adalah countdown total dari scheduled_end_at.
     */
    public function up(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            if (Schema::hasColumn('soal', 'timer_per_soal')) {
                $table->dropColumn('timer_per_soal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            if (! Schema::hasColumn('soal', 'timer_per_soal')) {
                $table->unsignedInteger('timer_per_soal')->default(60)->after('jawaban_benar');
            }
        });
    }
};
