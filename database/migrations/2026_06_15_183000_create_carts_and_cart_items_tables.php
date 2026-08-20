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
        // Tabela principal do carrinho
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            // Vincula ao usuário se ele estiver logado (essencial para a recuperação)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            // Guarda o ID da sessão para o caso de usuários visitantes (não logados)
            $table->string('session_id')->nullable()->index();
            // Salva o código do cupom se houver um aplicado
            $table->string('coupon_code')->nullable();
            $table->timestamps();
        });

        // Tabela com os itens de cada carrinho
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantidade')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
