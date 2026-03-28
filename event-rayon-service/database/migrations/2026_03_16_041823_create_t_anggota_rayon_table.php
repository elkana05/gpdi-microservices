<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_anggota_rayon', function (Blueprint $table) {
            $table->id();
            // Foreign key yang mengarah ke tabel m_rayon di database yang sama
            $table->foreignId('id_rayon')->constrained('m_rayon')->onDelete('cascade');
            $table->uuid('id_jemaat'); // UUID User Jemaat (dari User Service)
            $table->date('tanggal_bergabung')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_anggota_rayon');
    }
};