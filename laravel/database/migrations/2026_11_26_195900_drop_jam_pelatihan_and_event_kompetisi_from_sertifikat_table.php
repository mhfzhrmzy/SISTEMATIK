<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columnsToDrop = array_values(array_filter(
            ['jam_pelatihan', 'event_kompetisi'],
            fn (string $column) => Schema::hasColumn('sertifikat', $column)
        ));

        if (! empty($columnsToDrop)) {
            Schema::table('sertifikat', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }

    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            if (! Schema::hasColumn('sertifikat', 'jam_pelatihan')) {
                $table->unsignedInteger('jam_pelatihan')->nullable()->after('deskripsi');
            }
            if (! Schema::hasColumn('sertifikat', 'event_kompetisi')) {
                $table->string('event_kompetisi')->nullable()->after('jam_pelatihan');
            }
        });
    }
};
