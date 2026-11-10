<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('sertifikats');
    }

    public function down(): void
    {
        // Tabel lama tidak perlu dibuat ulang, karena sudah digantikan
        // sepenuhnya oleh tabel 'sertifikat'.
    }
};
