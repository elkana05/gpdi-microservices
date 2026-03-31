<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_pengumuman', function (Blueprint $table) {
            $table->id(); 
            $table->string('judul', 255);
            $table->text('isi');
            $table->string('lampiran', 255)->nullable(); 
            $table->uuid('id_pembuat')->nullable(); 
            // Menggunakan ENUM agar data terkunci hanya pada 3 pilihan ini
            $table->enum('scope', ['publik', 'jemaat', 'rayon'])->default('publik');
            $table->unsignedBigInteger('id_rayon')->nullable(); 
            $table->string('status', 50)->default('Aktif');
            $table->dateTime('published_at')->nullable(); 
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_pengumuman');
    }
};