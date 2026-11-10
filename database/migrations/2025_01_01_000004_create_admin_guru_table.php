<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_guru', function (Blueprint $table) {
            $table->string('nip', 18)->primary();
            $table->string('nama_lengkap', 60);
            $table->string('password');
            $table->string('foto_profile')->nullable();
            $table->string('dibuat_oleh', 18)->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('dibuat_oleh')->references('nip')->on('super_admin')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_guru');
    }
};
