<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('sku')->unique(); // Essencial para o Conta Azul
        $table->text('descricao')->nullable();

        // Preços por Tier
        $table->decimal('preco_black', 10, 2);
        $table->decimal('preco_gold', 10, 2);
        $table->decimal('preco_diamante', 10, 2);

        $table->integer('estoque')->default(0);
        $table->string('imagem')->nullable();

        // Campos de Integração (Reservados)
        $table->string('conta_azul_id')->nullable()->index();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
