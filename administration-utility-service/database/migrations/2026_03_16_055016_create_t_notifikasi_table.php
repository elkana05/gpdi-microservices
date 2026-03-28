<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_notifikasi', function (Blueprint $table) {
            $table->id(); // BIGINT
            $table->uuid('id_pengguna')->nullable(); // UUID user tujuan
            $table->string('judul', 255);
            $table->text('isi');
            $table->boolean('is_read')->default(false);
            $table->string('jenis_referensi', 50)->nullable(); // contoh: 'Pengumuman'
            $table->bigInteger('id_referensi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_notifikasi');
    }
};