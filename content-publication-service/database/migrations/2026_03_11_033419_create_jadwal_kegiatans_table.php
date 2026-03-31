<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_jadwal_kegiatan', function (Blueprint $table) {
            $table->id(); // BIGINT Auto Increment
            $table->string('nama_kegiatan', 255);
            $table->string('kategori', 50)->nullable();
            $table->date('tanggal'); // DATEONLY di Sequelize
            $table->time('waktu'); // Waktu (jam) kegiatan
            $table->text('lokasi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->uuid('id_pembuat')->nullable(); // UUID tanpa FK constraint
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_jadwal_kegiatan');
    }
};