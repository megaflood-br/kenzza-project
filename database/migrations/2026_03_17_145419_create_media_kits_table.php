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
    Schema::create('media_kits', function (Blueprint $table) {
        $table->id();
        $table->string('titulo');
        $table->string('categoria'); // Ex: Logos, Redes Sociais, Lancamentos
        $table->string('arquivo_path');
        $table->string('tipo')->nullable(); // png, jpg, mp4
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_kits');
    }
};
