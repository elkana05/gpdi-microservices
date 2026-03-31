<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
        {
            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->uuid('user_id');
                $table->string('full_name', 255);
                $table->string('phone_number', 50)->nullable();
                $table->text('address')->nullable();
                $table->bigInteger('rayon_id')->nullable(); // Tanpa FK constraint karena m_rayon ada di Event Service
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
