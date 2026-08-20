<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // 1. Renomeia preco_black para preco_distribuidor
            // Nota: Se der erro de "doctrine/dbal", veja o passo 3 abaixo
            $table->renameColumn('preco_black', 'preco_distribuidor');

            // 2. Remove as colunas que não serão mais usadas
            $table->dropColumn(['preco_gold', 'preco_diamante', 'preco_varejo']);

            // 3. Garante que campos de peso/dimensões existam caso queira integrar frete depois
            if (!Schema::hasColumn('products', 'peso')) {
                $table->decimal('peso', 8, 2)->default(0)->after('estoque');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('preco_distribuidor', 'preco_black');
            $table->decimal('preco_gold', 10, 2)->nullable();
            $table->decimal('preco_diamante', 10, 2)->nullable();
            $table->decimal('preco_varejo', 10, 2)->nullable();
        });
    }
};
