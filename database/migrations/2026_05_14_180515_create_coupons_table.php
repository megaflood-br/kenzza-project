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
    Schema::create('coupons', function (Blueprint $table) {
        $table->id();
        $table->string('codigo')->unique(); // Ex: KENZZA10
        $table->enum('tipo', ['percentual', 'frete_gratis']);
        $table->decimal('valor', 10, 2)->nullable(); // Ex: 10.00 para 10%
        $table->integer('limite_uso')->default(0); // 0 = ilimitado
        $table->integer('vezes_usado')->default(0);
        $table->date('validade')->nullable();
        $table->boolean('ativo')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
