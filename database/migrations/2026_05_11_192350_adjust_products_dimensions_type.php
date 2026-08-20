<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Alterando as colunas de INT para DECIMAL para suportar medidas quebradas (ex: 10.5 cm)
            // 8 dígitos no total, 2 após a vírgula.
            $table->decimal('largura', 8, 2)->default(0)->change();
            $table->decimal('altura', 8, 2)->default(0)->change();
            $table->decimal('comprimento', 8, 2)->default(0)->change();

            // Garantindo que o peso tenha 3 casas decimais (importante para gramas: 0.300kg)
            $table->decimal('peso', 8, 3)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('largura')->change();
            $table->integer('altura')->change();
            $table->integer('comprimento')->change();
            $table->decimal('peso', 8, 2)->change();
        });
    }
};
