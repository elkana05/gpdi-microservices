<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mendefinisikan nama tabel secara eksplisit menjadi 't_pengumuman'
        Schema::create('t_pengumuman', function (Blueprint $table) {
            $table->id(); // BIGINT Auto Increment
            $table->string('judul', 255);
            $table->text('isi');
            $table->string('lampiran', 255)->nullable(); // Dibuat nullable karena tidak semua pengumuman ada lampiran
            $table->uuid('id_pembuat')->nullable(); // UUID tanpa Foreign Key constraint (karena beda microservice)
            $table->string('scope', 50);
            $table->bigInteger('id_rayon')->nullable(); // Tanpa FK constraint
            $table->string('status', 50);
            $table->dateTime('published_at')->nullable(); // Menggunakan dateTime
            $table->timestamps(); // Otomatis membuat created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_pengumuman');
    }
};