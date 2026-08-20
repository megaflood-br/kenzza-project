<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action');
                $table->string('auditable_type');
                $table->unsignedBigInteger('auditable_id');
                $table->text('description');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_admin_can_open_user_profile_with_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create([
            'name' => 'Maria Souza',
            'role' => 'consumer',
            'phone' => '11988887777',
            'city' => 'Campinas',
            'state' => 'SP',
        ]);

        Order::create([
            'user_id' => $customer->id,
            'total' => 207,
            'status' => 'pago',
            'metodo_pagamento' => 'cartao',
            'external_id' => 'KENZZA-PROFILE-1',
        ]);

        $this->actingAs($admin)
            ->get(route('users.show', $customer))
            ->assertOk()
            ->assertSee('Maria Souza', false)
            ->assertSee('Pedidos feitos', false)
            ->assertSee('R$ 207,00', false)
            ->assertSee('pago', false)
            ->assertSee('Campinas', false);
    }

    public function test_users_index_links_to_profile(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['name' => 'Cliente Perfil', 'role' => 'consumer']);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee(route('users.show', $customer), false)
            ->assertSee('Perfil', false);
    }

    public function test_manager_cannot_open_non_representative_profile(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $customer = User::factory()->create(['role' => 'consumer']);

        $this->actingAs($manager)
            ->get(route('users.show', $customer))
            ->assertRedirect(route('users.index'));
    }
}
