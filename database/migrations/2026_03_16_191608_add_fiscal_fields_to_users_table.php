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
            // Verifica se a coluna não existe antes de criar (evita conflitos)
            if (!Schema::hasColumn('users', 'contaazul_id')) {
                $table->string('contaazul_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'document')) {
                $table->string('document')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'zip_code')) {
                $table->string('zip_code')->nullable();
            }
            if (!Schema::hasColumn('users', 'street')) {
                $table->string('street')->nullable();
            }
            if (!Schema::hasColumn('users', 'number')) {
                $table->string('number')->nullable();
            }
            if (!Schema::hasColumn('users', 'complement')) {
                $table->string('complement')->nullable();
            }
            if (!Schema::hasColumn('users', 'neighborhood')) {
                $table->string('neighborhood')->nullable();
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state', 2)->nullable();
            }

            // Controle de Perfil Completo para o bloqueio do distribuidor
            if (!Schema::hasColumn('users', 'profile_completed')) {
                $table->boolean('profile_completed')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'contaazul_id', 'document', 'phone', 'zip_code',
                'street', 'number', 'complement', 'neighborhood',
                'city', 'state', 'profile_completed'
            ]);
        });
    }
};
