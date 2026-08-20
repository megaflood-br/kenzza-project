<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\InfinitePayService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InfinitePayCheckoutTest extends TestCase
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

    public function test_infinitepay_checkout_url_uses_handle_amount_and_order_id(): void
    {
        $user = User::factory()->create([
            'document' => '12345678901',
            'email' => 'cliente@example.com',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 121.08,
            'status' => 'pendente',
            'external_id' => 'KENZZA-123',
            'metodo_pagamento' => 'cartao',
        ]);

        $url = InfinitePayService::checkoutUrl($order, $user);

        $this->assertStringStartsWith('https://pay.infinitepay.io/', $url);
        $this->assertStringContainsString('/121,08?', $url);
        $this->assertStringContainsString('order_id=KENZZA-123', $url);
        $this->assertStringContainsString('cd=12345678901', $url);
        $this->assertStringContainsString('email=cliente%40example.com', $url);
        $this->assertStringContainsString('redirect_url=', $url);
        $this->assertStringNotContainsString('asaas', strtolower($url));
    }

    public function test_card_checkout_redirects_to_infinitepay(): void
    {
        $user = User::factory()->create([
            'email' => 'comprador@example.com',
            'document' => '39053344705',
            'zip_code' => '12951110',
        ]);

        $product = Product::create([
            'nome' => 'Shampoo Teste',
            'sku' => 'KEN-TEST-01',
            'descricao' => 'Produto de teste',
            'preco_distribuidor' => 50,
            'estoque' => 10,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'quantidade' => 1,
                        'preco' => 50,
                    ],
                ],
            ])
            ->post(route('checkout.pagar'), [
                'payment_method' => 'cartao',
                'shipping_value' => 15,
                'shipping_service_id' => 1,
            ]);

        $response->assertRedirect();
        $this->assertStringContainsString('pay.infinitepay.io', (string) $response->headers->get('Location'));
        $this->assertStringNotContainsString('asaas', strtolower((string) $response->headers->get('Location')));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'metodo_pagamento' => 'cartao',
            'status' => 'pendente',
        ]);
    }

    public function test_infinitepay_webhook_marks_order_as_paid(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total' => 80,
            'status' => 'pendente',
            'external_id' => 'KENZZA-WEBHOOK-1',
            'metodo_pagamento' => 'cartao',
        ]);

        $this->postJson('/webhook/infinitepay', [
            'order_id' => 'KENZZA-WEBHOOK-1',
            'status' => 'approved',
        ])->assertOk()->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pago',
        ]);
    }

    public function test_legacy_asaas_webhook_still_marks_in_flight_orders_as_paid(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total' => 80,
            'status' => 'pendente',
            'external_id' => 'pay_asaas_legado',
            'metodo_pagamento' => 'cartao',
        ]);

        $this->postJson('/webhook/asaas', [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => ['id' => 'pay_asaas_legado'],
        ])->assertOk()->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pago',
        ]);
    }
}
