<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_pilihan_surat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_surat', 255);
            $table->text('whatsapp_url');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_pilihan_surat');
    }
};