<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('worship_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255); // Contoh: "Ibadah Raya Sesi 1"
            $table->string('category', 100); // BARU: "Ibadah Raya", "Ibadah Pemuda", "Ibadah Kaum Ibu"
            $table->string('day_of_week', 50); // BARU: "Minggu", "Sabtu", "Kamis"
            $table->text('description')->nullable();
            $table->string('location', 255);
            $table->date('event_date')->nullable(); // Opsional jika ibadah ini spesifik tanggal tertentu
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('status_publish', 50)->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worship_schedules');
    }
};