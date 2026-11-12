<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurusan', function (Blueprint $table) {
            $table->tinyIncrements('id_jurusan');
            $table->string('nama_jurusan', 60)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurusan');
    }
};
