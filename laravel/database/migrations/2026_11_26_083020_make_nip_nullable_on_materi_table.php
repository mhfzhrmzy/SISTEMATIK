<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS `v_materi`');

        Schema::table('materi', function (Blueprint $table) {
            $table->string('nip', 18)->nullable()->change();
        });

        $driver = DB::getDriverName();
        $concatExpr = $driver === 'sqlite'
            ? "k.`tingkat` || ' ' || k.`nomor_kelas`"
            : "CONCAT(k.`tingkat`, ' ', k.`nomor_kelas`)";

        DB::statement("
            CREATE VIEW `v_materi` AS
            SELECT m.`id_materi`, m.`nip`, m.`id_kelas`,
                   j.`nama_jurusan` AS `jurusan`,
                   {$concatExpr} AS `kelas`,
                   k.`tahun`, m.`judul_materi`, m.`isi_materi`, m.`upload_file`
            FROM `materi` m
            LEFT JOIN `kelas` k   ON k.`id_kelas`   = m.`id_kelas`
            LEFT JOIN `jurusan` j ON j.`id_jurusan` = k.`id_jurusan`
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('materi')->whereNull('nip')->update(['nip' => '']);

        Schema::table('materi', function (Blueprint $table) {
            $table->string('nip', 18)->nullable(false)->change();
        });
    }
};
