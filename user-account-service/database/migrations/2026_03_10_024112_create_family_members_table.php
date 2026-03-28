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
    Schema::create('family_members', function (Blueprint $table) {
        $table->id();
        $table->uuid('user_id'); // ID dari user (Jemaat Aktif) yang memasukkan data keluarga ini
        $table->string('full_name', 255);
        $table->string('relationship', 50); // Contoh: 'Suami', 'Istri', 'Anak'
        $table->string('gender', 20)->nullable();
        $table->date('birth_date')->nullable();
        $table->timestamps();
        $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
