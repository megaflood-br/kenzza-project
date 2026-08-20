<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SyncBaseLinker extends Command
{
    protected $signature = 'baselinker:sync';

    public function handle()
    {
        $this->info("Iniciando sincronização BaseLinker...");
        $token = config('services.baselinker.token');
        $startTime = time() - (86400 * 7); // Últimos 7 dias

        $response = Http::asForm()->post('https://api.baselinker.com/connector.php', [
            'token' => $token,
            'method' => 'getOrders',
            'parameters' => json_encode([
                'date_from' => $startTime,
                'get_unconfirmed_orders' => true
            ])
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $qtd = count($data['orders'] ?? []);
            $this->info("A API retornou {$qtd} pedidos.");

            if (($data['status'] ?? '') === 'SUCCESS' && !empty($data['orders'])) {
                foreach ($data['orders'] as $orderData) {
                    $this->processarPedido($orderData);
                }
                $this->info("Sincronização concluída com sucesso.");
            }
        } else {
            $this->error("Falha ao comunicar com a API do BaseLinker.");
        }
    }

    private function processarPedido($d)
    {
        $baseOrderId = $d['order_id'] ?? null;
        if (!$baseOrderId) return;

        // 1. UTILIZADOR
        $email = !empty($d['email']) ? $d['email'] : "cliente_{$baseOrderId}@kenzza.com.br";
        $nome = !empty($d['delivery_fullname']) ? $d['delivery_fullname'] : (!empty($d['invoice_fullname']) ? $d['invoice_fullname'] : "Cliente Pedido {$baseOrderId}");

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = new User();
            $user->email = $email;
            $user->password = Hash::make(Str::random(16));
            $user->role = 'consumer'; // Garantindo o nível correto do sistema
        }

        // Atualização forçada dos dados do cliente
        $user->name = $nome;
        $user->phone = !empty($d['phone']) ? $d['phone'] : '00000000000';
        $user->document = !empty($d['invoice_nip']) ? preg_replace('/\D/', '', $d['invoice_nip']) : null;
        $user->street = !empty($d['delivery_address']) ? $d['delivery_address'] : 'Não Informado';
        $user->number = '';
        $user->neighborhood = 'Marketplace';
        $user->city = !empty($d['delivery_city']) ? $d['delivery_city'] : 'Atibaia';
        $user->state = !empty($d['delivery_state']) ? $d['delivery_state'] : 'SP';
        $user->zip_code = !empty($d['delivery_postcode']) ? preg_replace('/\D/', '', $d['delivery_postcode']) : '12940000';

        $user->save();

        // 2. MATEMÁTICA DOS PREÇOS E PREPARAÇÃO DOS ITENS
        $totalProdutos = 0.00;
        $produtosParaSalvar = [];

        if (isset($d['products']) && is_array($d['products'])) {
            foreach ($d['products'] as $p) {
                $totalProdutos += (float)($p['price_brutto'] ?? 0) * (float)($p['quantity'] ?? 1);
                $produtosParaSalvar[] = $p;
            }
        }

        $frete = (float)($d['delivery_price'] ?? 0);
        $totalFinal = $totalProdutos + $frete;

        // NOVO: Captura o valor exato que o cliente já pagou no BaseLinker
        $valorPago = (float)($d['payment_done'] ?? 0);

        // 3. ENCOMENDA
        $order = Order::where('base_order_id', $baseOrderId)->first();
        if (!$order) {
            $order = new Order();
            $order->base_order_id = $baseOrderId;
        }

        $order->user_id = $user->id;
        $order->origin = strtolower(!empty($d['order_source']) ? $d['order_source'] : (!empty($d['order_page']) ? $d['order_page'] : 'marketplace'));
        $order->marketplace_order_id = !empty($d['external_order_id']) ? $d['external_order_id'] : null;
        $order->metodo_pagamento = strtolower(!empty($d['payment_method']) ? $d['payment_method'] : 'marketplace');
        $order->total = round($totalFinal, 2);
        $order->frete = round($frete, 2);

        // LÓGICA INTELIGENTE DE STATUS DE PAGAMENTO
        if ($valorPago >= $totalFinal && $totalFinal > 0) {
            // Se o valor pago no BaseLinker cobriu o total, marca como pago
            $order->status = 'aprovado';
        } else {
            // Se não, e se for um pedido novo, marca como pendente
            if (!$order->exists) {
                $order->status = 'pendente';
            }
        }

        $order->save();

        // 4. ITENS DO PEDIDO
        foreach ($produtosParaSalvar as $prod) {
            $quantidade = (float)($prod['quantity'] ?? 1);
            $precoUnitario = (float)($prod['price_brutto'] ?? 0);
            $subtotal = $quantidade * $precoUnitario;
            $productId = $prod['product_id'];
            $nomeProduto = !empty($prod['name']) ? $prod['name'] : "Produto BaseLinker {$productId}";

            $produtoBd = Product::find($productId);
            if (!$produtoBd) {
                $produtoBd = new Product();
                $produtoBd->id = $productId;
                $produtoBd->nome = $nomeProduto;
                $produtoBd->slug = Str::slug($nomeProduto . '-' . $productId);
                $produtoBd->preco_distribuidor = 0;
                $produtoBd->save();
            }

            $orderItem = OrderItem::where('order_id', $order->id)->where('product_id', $productId)->first();
            if (!$orderItem) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $productId;
            }

            $orderItem->quantidade = $quantidade;
            $orderItem->preco_unitario = $precoUnitario;
            $orderItem->subtotal = $subtotal;
            $orderItem->save();
        }

        Log::info("SYNC OK: Pedido {$baseOrderId} | Utilizador (ID: {$user->id}), Pedido Status ({$order->status}) e " . count($produtosParaSalvar) . " Itens salvos.");
    }
}
