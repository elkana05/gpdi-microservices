<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profil_gereja', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gereja');
            $table->text('sejarah')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->text('pengakuan_iman')->nullable();
            $table->string('alamat');
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('maps_url')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_gereja');
    }
};
