<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('renungan_harian', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('ayat_alkitab');
            $table->text('isi_renungan');
            $table->date('tanggal_publikasi');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->unsignedBigInteger('dibuat_oleh'); // user_id dari Auth Service
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renungan_harian');
    }
};
