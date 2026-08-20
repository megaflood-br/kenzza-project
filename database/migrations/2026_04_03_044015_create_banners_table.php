<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->string('imagem_desktop'); // Banner para PC
            $table->string('imagem_mobile');  // Banner para Celular
            $table->string('link')->nullable(); // Link para onde o banner leva
            $table->integer('ordem')->default(0); // Para ordenar os slides
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
