<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique();
        $table->string('value');
        $table->timestamps();
    });

    // Inserir valores padrão (Distribuidor é a base, então é 0% ou 1)
    DB::table('settings')->insert([
        ['key' => 'margin_salon', 'value' => '120'], // +120%
        ['key' => 'margin_consumer', 'value' => '140'], // +140%
    ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
