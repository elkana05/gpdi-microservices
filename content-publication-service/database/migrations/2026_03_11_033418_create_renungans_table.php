<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mendefinisikan nama tabel secara eksplisit menjadi 't_renungan'
        Schema::create('t_renungan', function (Blueprint $table) {
            $table->id(); // BIGINT Auto Increment
            $table->string('tema', 255);
            $table->string('ayat_pokok', 255);
            $table->text('isi');
            $table->uuid('id_penulis')->nullable(); // UUID penulis (tanpa FK constraint)
            $table->string('status', 50);
            $table->dateTime('published_at')->nullable();
            $table->timestamps(); // Otomatis membuat created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_renungan');
    }
};