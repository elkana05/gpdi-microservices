<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_rayon', function (Blueprint $table) {
            $table->id(); // BIGINT Auto Increment
            $table->string('nama_rayon', 100);
            $table->uuid('id_ketua_rayon')->nullable(); // UUID User Ketua Rayon (dari User Service)
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_rayon');
    }
};