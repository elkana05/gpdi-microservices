<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_profil_gereja', function (Blueprint $table) {
            $table->id(); // BIGINT Auto Increment
            $table->string('nama_gereja', 255);
            $table->text('ayat_tahunan')->nullable();
            $table->text('sejarah')->nullable();
            $table->text('visi_misi')->nullable();
            $table->text('pengakuan_iman')->nullable();
            $table->text('struktur_pelayanan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nomor_kontak', 50)->nullable();
            $table->text('link_maps')->nullable();
            $table->text('link_sosmed')->nullable();
            $table->string('foto_pendeta', 255)->nullable();
            $table->string('banner_beranda', 255)->nullable();
            $table->uuid('id_pengubah')->nullable(); // UUID tanpa FK constraint
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_profil_gereja');
    }
};