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
    Schema::table('users', function (Blueprint $table) {
        // ID do Cliente no ContaAzul (essencial para faturar pedidos)
        $table->string('contaazul_id')->nullable()->unique()->after('id');

        // Dados Fiscais
        $table->string('document')->nullable()->unique(); // CPF ou CNPJ
        $table->string('phone')->nullable();

        // Endereço (O ContaAzul exige para emitir nota)
        $table->string('zip_code')->nullable();
        $table->string('street')->nullable();
        $table->string('number')->nullable();
        $table->string('complement')->nullable();
        $table->string('neighborhood')->nullable();
        $table->string('city')->nullable();
        $table->string('state', 2)->nullable(); // UF
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
