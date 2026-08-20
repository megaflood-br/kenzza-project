<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
    Schema::table('product_sheets', function ($table) {
        $table->dropColumn('diferenciais');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_sheets', function (Blueprint $table) {
            //
        });
    }
};
