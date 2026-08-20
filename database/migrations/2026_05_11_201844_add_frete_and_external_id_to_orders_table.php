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
        // Verifica se a coluna 'frete' NÃO existe antes de criar
        if (!Schema::hasColumn('orders', 'frete')) {
            $table->decimal('frete', 10, 2)->default(0)->after('total');
        }

        // Verifica se a coluna 'external_id' NÃO existe antes de criar
        if (!Schema::hasColumn('orders', 'external_id')) {
            $table->string('external_id')->nullable()->after('frete');
        }
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['frete', 'external_id']);
    });
}
};
