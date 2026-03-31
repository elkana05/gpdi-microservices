<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rayon_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rayon_id')->constrained('rayons')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            // Menyimpan siapa yang membuat (Data dari JWT / User Service)
            $table->unsignedBigInteger('created_by_user_id'); 
            $table->string('created_by_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rayon_schedules');
    }
};