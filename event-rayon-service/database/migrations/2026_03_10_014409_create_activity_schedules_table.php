<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: Kerja Bakti Gereja
            $table->text('description')->nullable();
            $table->string('location');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status_publish')->default('published');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_schedules');
    }
};