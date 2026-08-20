<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function River()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            // ID do usuário que fez a ação (nulo para ações do sistema/webhooks)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->string('action'); // 'create', 'update', 'delete'
            $table->string('auditable_type'); // Ex: 'App\Models\Product'
            $table->unsignedBigInteger('auditable_id'); // ID do registro afetado

            $table->text('description'); // Frase amigável: "Carlos atualizou o produto Shampoo K'enzza"
            $table->json('old_values')->nullable(); // Como os dados eram antes
            $table->json('new_values')->nullable(); // Como os dados ficaram depois

            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Índices para buscas rápidas no painel admin
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
