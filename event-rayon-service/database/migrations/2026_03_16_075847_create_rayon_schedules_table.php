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
        Schema::create('rayon_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rayon_id')->constrained('m_rayon')->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('location', 255);
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->uuid('created_by_user_id');
            $table->string('created_by_name', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rayon_schedules');
    }
};
