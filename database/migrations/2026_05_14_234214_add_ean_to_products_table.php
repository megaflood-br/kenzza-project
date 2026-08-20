<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $blueprint) {
            // O EAN geralmente tem 13 dígitos, mas usamos string para manter zeros à esquerda
            $blueprint->string('ean', 15)->nullable()->after('nome');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $blueprint) {
            $blueprint->dropColumn('ean');
        });
    }
};
