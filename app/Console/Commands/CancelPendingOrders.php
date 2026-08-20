<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CancelPendingOrders extends Command
{
    protected $signature = 'orders:cancel-pending';
    protected $description = 'Cancela pedidos pendentes com mais de 36 horas e devolve o estoque';

    public function handle()
    {
        // AJUSTE: Agora buscando exatamente o termo 'pendente' que vimos no DBeaver
        $expiredOrders = Order::where('status', 'pendente')
            ->where('created_at', '<=', Carbon::now()->subHours(36))
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('Nenhum pedido pendente expirado encontrado.');
            return;
        }

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                // 1. Devolve o estoque dos itens do pedido
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        // Usando 'quantidade' que confirmamos antes
                        $product->increment('estoque', $item->quantidade);
                    }
                }

                // 2. Altera o status para 'cancelado' (em português conforme seu padrão)
                $order->update(['status' => 'cancelado']);

                $this->info("Pedido #{$order->id} cancelado e estoque devolvido.");
            });
        }

        $this->info('Processo concluído com sucesso.');
    }
}
