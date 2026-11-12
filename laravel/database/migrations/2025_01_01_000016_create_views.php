<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS `v_leaderboard`');
        DB::statement('
            CREATE VIEW `v_leaderboard` AS
            SELECT l.`id_leaderboard`, l.`id_quiz`, l.`nisn`, l.`total_poin`,
                   RANK() OVER (PARTITION BY l.`id_quiz` ORDER BY l.`total_poin` DESC) AS `peringkat`,
                   l.`created_at`, l.`updated_at`
            FROM `leaderboard` l
        ');

        DB::statement('DROP VIEW IF EXISTS `v_poin_siswa`');
        DB::statement('
            CREATE VIEW `v_poin_siswa` AS
            SELECT s.`nisn`, COALESCE(SUM(l.`total_poin`), 0) AS `poin`
            FROM `pengguna_siswa` s
            LEFT JOIN `leaderboard` l ON l.`nisn` = s.`nisn`
            GROUP BY s.`nisn`
        ');

        DB::statement('DROP VIEW IF EXISTS `v_siswa`');
        $concatExpr = DB::getDriverName() === 'sqlite'
            ? 'k.`tingkat` || " " || k.`rombel`'
            : "CONCAT(k.`tingkat`, ' ', k.`rombel`)";

        DB::statement("
            CREATE VIEW `v_siswa` AS
            SELECT s.`nisn`, s.`nama_lengkap`, s.`id_kelas`,
                   j.`nama_jurusan` AS `jurusan`,
                   {$concatExpr} AS `kelas`,
                   k.`tahun`, s.`foto_profile`
            FROM `pengguna_siswa` s
            JOIN `kelas` k   ON k.`id_kelas`   = s.`id_kelas`
            JOIN `jurusan` j ON j.`id_jurusan` = k.`id_jurusan`
        ");

        DB::statement('DROP VIEW IF EXISTS `v_materi`');
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

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS `v_materi`');
        DB::statement('DROP VIEW IF EXISTS `v_siswa`');
        DB::statement('DROP VIEW IF EXISTS `v_poin_siswa`');
        DB::statement('DROP VIEW IF EXISTS `v_leaderboard`');
    }
};
