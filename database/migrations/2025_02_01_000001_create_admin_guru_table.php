<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_guru', function (Blueprint $table) {
            $table->string('nip', 18)->primary(); // 18 digit, diisi manual (bukan auto-increment)
            $table->string('nama_lengkap', 60);
            $table->string('password');
            $table->string('foto_profile')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_guru');
    }
};
