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
        Schema::table('orders', function (Blueprint $table) {
            // Adiciona os novos campos de rastreio logo após o ID
            $table->string('origin')->nullable()->after('id')->comment('shopee, mercadolivre, ecommerce, etc');
            $table->string('base_order_id')->nullable()->unique()->after('origin')->comment('ID único do Base.com');
            $table->string('marketplace_order_id')->nullable()->after('base_order_id')->comment('ID do pedido na loja original');

            // Torna o user_id opcional (nullable) para os pedidos de marketplaces
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['origin', 'base_order_id', 'marketplace_order_id']);

            // Reverte o user_id para obrigatório
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
