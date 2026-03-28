<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_pelayanan', function (Blueprint $table) {
            $table->id(); // BIGINT Auto Increment
            $table->string('nama_pelayanan', 255);
            $table->text('deskripsi')->nullable();
            $table->string('gambar', 255)->nullable(); // Path gambar pelayanan
            $table->uuid('id_pembuat')->nullable(); // UUID tanpa FK constraint
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_pelayanan');
    }
};