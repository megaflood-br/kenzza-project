<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $blueprint) {
            // Cria a coluna de comissão logo após o frete, aceitando nulo por padrão (para e-commerce)
            $blueprint->decimal('comissao_gerente', 10, 2)->nullable()->after('frete');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $blueprint) {
            $blueprint->dropColumn('comissao_gerente');
        });
    }
};
