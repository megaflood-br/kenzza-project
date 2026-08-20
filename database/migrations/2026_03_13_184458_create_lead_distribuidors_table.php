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
    Schema::create('lead_distribuidors', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('whatsapp');
        $table->string('email')->nullable();
        $table->string('cidade'); // Campo novo
        $table->string('estado', 2); // Campo novo (ex: SP, RJ)
        $table->enum('status', ['novo', 'em_atendimento', 'aprovado', 'descartado'])->default('novo');
        $table->text('observacoes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_distribuidors');
    }
};
