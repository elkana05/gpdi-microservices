<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_galeri', function (Blueprint $table) {
            $table->id(); // BIGINT Auto Increment
            $table->string('judul', 255);
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_kegiatan'); // Kronologis foto
            $table->string('path_foto', 255); // URL/Path gambar
            $table->uuid('id_pengunggah')->nullable(); // UUID tanpa FK constraint
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_galeri');
    }
};