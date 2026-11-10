<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('judul_sertifikat');
            $table->unsignedInteger('jam_pelatihan')->nullable()->after('deskripsi');
            $table->string('event_kompetisi')->nullable()->after('jam_pelatihan');
            $table->string('badge_url')->nullable()->after('file_hash');
            $table->boolean('terverifikasi')->default(false)->after('badge_url');
        });
    }

    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropColumn([
                'kategori',
                'jam_pelatihan',
                'event_kompetisi',
                'badge_url',
                'terverifikasi',
            ]);
        });
    }
};
