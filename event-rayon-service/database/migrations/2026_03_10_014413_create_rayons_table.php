<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rayons', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Rayon 1
            $table->string('area')->nullable(); // Area cakupan rayon
            $table->unsignedBigInteger('ketua_user_id')->nullable(); // Merujuk ke ID User di User Service
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rayons');
    }
};