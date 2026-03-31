<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worship_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: Ibadah Raya 1
            $table->text('description')->nullable();
            $table->string('location');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status_publish')->default('published'); // draft / published
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worship_schedules');
    }
};