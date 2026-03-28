<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('informasi_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelayanan');
            $table->text('deskripsi');
            $table->string('target_usia')->nullable();
            $table->string('jadwal')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informasi_pelayanan');
    }
};
