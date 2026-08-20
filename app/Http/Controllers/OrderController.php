<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\OrderTrackingCode;
// Importamos o Service do Melhor Envio
use App\Services\MelhorEnvioService;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
                       ->with(['items.product'])
                       ->latest()
                       ->paginate(10);

        return view('distributor.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::orderBy('nome', 'asc')->get();
        return view('distributor.orders.create', compact('products'));
    }

    /**
     * Passo 1: Recebe a grade de produtos e leva para a tela de Revisão
     */
    public function reviewOrder(Request $request)
    {
        $items = $request->input('items', []);
        $orderedItems = collect($items)->filter(fn($qty) => (int)$qty > 0);

        if ($orderedItems->isEmpty()) {
            return back()->with('error', 'Selecione pelo menos um produto.');
        }

        $summary = [];
        $totalGeral = 0;

        // Se for representante, aplicamos a margem de 30% na criação do pedido em massa
        $isRepresentative = auth()->check() && auth()->user()->role === 'representative';

        foreach ($orderedItems as $id => $qty) {
            $product = Product::find($id);
            if ($product) {
                // Buscando o preço base de distribuidor
                $precoBruto = $product->preco_distribuidor;

                // Lógica de tratamento de preço mais inteligente e segura
                if (is_string($precoBruto)) {
                    if (str_contains($precoBruto, ',')) {
                        // Se tem vírgula, é formato brasileiro (ex: 1.250,00)
                        $precoFloat = (float) str_replace(',', '.', str_replace('.', '', $precoBruto));
                    } else {
                        // Se não tem vírgula, é formato padrão de banco de dados (ex: 1250.00)
                        $precoFloat = (float) $precoBruto;
                    }
                } else {
                    $precoFloat = (float) $precoBruto;
                }

                // Se o usuário logado for representante, aumenta 30% no preço unitário
                if ($isRepresentative) {
                    $precoFloat = $precoFloat * 1.30;
                }

                $subtotal = $precoFloat * (int)$qty;
                $totalGeral += $subtotal;

                $summary[] = [
                    'id' => $id,
                    'nome' => $product->nome,
                    'qty' => (int)$qty,
                    'preco' => $precoFloat,
                    'subtotal' => $subtotal
                ];
            }
        }

        // SALVA NA SESSÃO
        session([
            'pending_order_items' => $summary,
            'pending_order_total' => $totalGeral
        ]);

        return view('distributor.orders.review', compact('summary', 'totalGeral'));
    }

    /**
     * Passo 2: Salva o pedido e os itens no banco de dados
     */
    public function storeOrder(Request $request)
    {
        $summary = session('pending_order_items');

        if (!$summary) {
            return redirect()->route('orders.create')->with('error', 'Sua sessão expirou ou está vazia. Por favor, refaça o pedido.');
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => auth()->id(),
                'status' => 'pendente',
                'origin' => auth()->user()->role === 'representative' ? 'representative' : 'distributor',
                'metodo_pagamento' => $request->payment_method,
                'total' => session('pending_order_total'),
                'frete' => 0,
            ]);

            foreach ($summary as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantidade' => $item['qty'],
                    'preco_unitario' => $item['preco'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            session()->forget(['pending_order_items', 'pending_order_total']);

            return redirect()->route('orders.index')->with('success', 'Pedido gerado com sucesso! Nossa equipe entrará em contato em breve para alinhar o faturamento e o envio.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar pedido em massa: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao salvar o pedido. Tente novamente.');
        }
    }

    /**
     * Listagem Administrativa com Eager Loading para o Modal e Filtro Travado
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        // REGRA DE OURO: Gerente comercial (Sandy) só vê representantes
        if (auth()->check() && auth()->user()->role === 'manager') {
            $query->where('origin', 'representative');
        }
        // REGRA ADMIN: Pode filtrar pelas abas
        else {
            if ($request->origin === 'distributor') {
                // Traz os que têm origin='distributor' OU que o usuário é distribuidor (legado)
                $query->where(function($q) {
                    $q->where('origin', 'distributor')
                      ->orWhereHas('user', function($u) { $u->where('role', 'distributor'); });
                });
            } elseif ($request->origin === 'ecommerce') {
                // Traz e-commerce ou pedidos sem origem definida (legado)
                $query->where(function($q) {
                    $q->where('origin', 'ecommerce')
                      ->orWhereNull('origin');
                });
            } elseif ($request->origin === 'representative') {
                // Traz especificamente a aba de representantes
                $query->where('origin', 'representative');
            }
        }

        $orders = $query->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Atualização de Status e Gerenciamento de Estoque
     */
    public function updateStatus(Request $request, Order $order)
    {
        $oldStatus = $order->status;
        $newStatus = $request->status;

        try {
            DB::beginTransaction();

            // Lógica de retorno ou baixa de estoque baseada na mudança de status
            if (in_array($newStatus, ['cancelado', 'devolvido_falha']) && !in_array($oldStatus, ['cancelado', 'devolvido_falha'])) {
                foreach ($order->items as $item) {
                    if ($item->product) $item->product->increment('estoque', $item->quantidade);
                }
            } elseif ($oldStatus === 'cancelado' && $newStatus !== 'cancelado') {
                foreach ($order->items as $item) {
                    if ($item->product) $item->product->decrement('estoque', $item->quantidade);
                }
            }

            $order->update([
                'status' => $newStatus,
                'codigo_rastreio' => $request->codigo_rastreio ?? $order->codigo_rastreio
            ]);

            DB::commit();

            // Notificação automática ao mudar status
            if ($oldStatus !== $newStatus) {
                try {
                    $order->user->notify(new OrderStatusUpdated($order));
                } catch (\Exception $e) {
                    Log::error("Erro Notificação Status: " . $e->getMessage());
                }
            }

            return back()->with('success', 'Pedido atualizado e cliente notificado!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar status: " . $e->getMessage());
            return back()->with('error', 'Falha ao atualizar o pedido.');
        }
    }

    /**
     * Geração da Etiqueta no Melhor Envio e envio do Código de Rastreio
     */
    public function gerarEtiqueta(Order $order, MelhorEnvioService $melhorEnvioService)
    {
        if (!$order->shipping_service_id) {
            return back()->with('error', 'Nenhum serviço de frete selecionado para este pedido.');
        }

        $resultado = $melhorEnvioService->gerarEtiquetaCompleta($order, $order->shipping_service_id);

        if (isset($resultado['sucesso']) && $resultado['sucesso']) {

            $order->update([
                'url_etiqueta' => $resultado['url_etiqueta'],
                'status' => 'enviado',
            ]);

            if (isset($resultado['rastreio'])) {
                $order->update(['codigo_rastreio' => $resultado['rastreio']]);
            }

            if ($order->codigo_rastreio) {
                try {
                    $order->user->notify(new OrderTrackingCode($order));
                } catch (\Exception $e) {
                    Log::error("Erro Notificação Rastreio: " . $e->getMessage());
                }
            } else {
                try {
                    $order->user->notify(new OrderStatusUpdated($order));
                } catch (\Exception $e) {
                    Log::error("Erro Notificação Status de Envio: " . $e->getMessage());
                }
            }

            return back()->with('success', 'Etiqueta gerada e rastreio enviado para o e-mail do cliente!');
        }

        return back()->with('error', 'Erro ao gerar etiqueta: ' . ($resultado['erro'] ?? 'Erro na comunicação com a API.'));
    }
}
