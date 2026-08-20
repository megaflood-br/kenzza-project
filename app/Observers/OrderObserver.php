<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updated(Order $order): void
{
    if ($order->isDirty('status') && !empty($order->base_order_id)) {

        // 1. Normaliza para minúsculas e remove espaços e underlines
        $statusAtual = strtolower(trim($order->status));
        $statusLimpo = str_replace(['_', ' '], '', $statusAtual);

        // 2. Remove acentos para garantir a comparação
        $statusLimpo = strtr(utf8_decode($statusLimpo), utf8_decode('àáâãäèéêëìíîïòóôõöùúûüñç'), 'aaaaaeeeeiiiiooooouuuunc');

        $statusMap = [
            'pendente' => 358923,
            'aprovado' => 358924,
            'pago' => 358924,
            'emseparacao' => 358925, // Captura tanto 'em_separacao' quanto 'em separacao'
            'enviado' => 358926,
            'cancelado' => 358927,
            'devolvidofalha' => 358928,
            'entregue' => 358935,
        ];

        $statusIdNoBaseLinker = $statusMap[$statusLimpo] ?? null;

        if ($statusIdNoBaseLinker) {
            $response = Http::asForm()->post('https://api.baselinker.com/connector.php', [
                'token' => config('services.baselinker.token'),
                'method' => 'setOrderStatus',
                'parameters' => json_encode([
                    'order_id' => $order->base_order_id,
                    'status_id' => $statusIdNoBaseLinker
                ])
            ]);

            if ($response->successful()) {
                Log::info("OBSERVER: Pedido {$order->base_order_id} atualizado no BaseLinker para: {$statusAtual}");
            } else {
                Log::error("OBSERVER ERRO: Falha ao mudar status no BaseLinker. Pedido: {$order->base_order_id}");
            }
        } else {
            Log::warning("OBSERVER: O status '{$statusAtual}' (limpo: {$statusLimpo}) não foi encontrado no mapa de IDs.");
        }
    }
}
}
