<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_galeri', function (Blueprint $table) {
            $table->id(); 
            $table->string('judul', 255);
            $table->string('kategori', 100)->default('Umum'); // TAMBAHAN KOLOM KATEGORI
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_kegiatan'); 
            $table->string('path_foto', 255); 
            $table->uuid('id_pengunggah')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_galeri');
    }
};