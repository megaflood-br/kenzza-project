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
        if (!Schema::hasColumn('orders', 'codigo_rastreio')) {
            $table->string('codigo_rastreio')->nullable();
        }

        if (!Schema::hasColumn('orders', 'status_envio')) {
            $table->string('status_envio')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['codigo_rastreio', 'status_envio']);
    });
}
};
