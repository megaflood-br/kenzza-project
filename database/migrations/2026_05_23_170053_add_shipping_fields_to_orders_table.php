<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('url_etiqueta', 500)->nullable();
            $table->string('melhor_envio_id')->nullable();
            $table->string('shipping_service_id')->nullable(); // Para salvar se foi PAC, SEDEX, etc
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['url_etiqueta', 'melhor_envio_id', 'shipping_service_id']);
        });
    }
};
