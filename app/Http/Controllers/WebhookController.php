<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\InfinitePayService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{
    /**
     * Webhook de pagamento. Prioriza InfinitePay e ainda aceita o payload
     * legado do Asaas para pedidos em trânsito gerados antes da troca.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        $asaasPaymentId = $payload['payment']['id'] ?? null;
        $asaasEvent = $payload['event'] ?? null;

        if ($asaasPaymentId && $asaasEvent) {
            return $this->handleAsaasLegacy($payload, $asaasPaymentId, $asaasEvent);
        }

        Log::info('Webhook InfinitePay Recebido:', $payload);

        $externalId = InfinitePayService::orderReferenceFromPayload($payload);

        if (! $externalId) {
            Log::warning('Webhook da InfinitePay recebido mas nenhum ID de pedido válido foi identificado no payload.');

            return response()->json(['status' => 'error', 'message' => 'ID de referência ausente'], 200);
        }

        $order = Order::with('user')->where('external_id', $externalId)->first();

        if (! $order) {
            Log::warning("Webhook InfinitePay recebido mas o external_id '{$externalId}' não foi localizado no banco.");

            return response()->json(['status' => 'not_found'], 200);
        }

        $statusLink = $payload['status'] ?? ($payload['data']['status'] ?? null);

        if (! InfinitePayService::isPaidStatus($statusLink)) {
            Log::info('Pedido localizado mas status ignorado pelo sistema: '.($statusLink ?? 'N/A'));

            return response()->json(['status' => 'ignored']);
        }

        $this->marcarPedidoComoPago($order, 'InfinitePay');

        return response()->json(['status' => 'ok']);
    }

    private function handleAsaasLegacy(array $payload, string $asaasPaymentId, string $event)
    {
        Log::info('Webhook Asaas legado recebido:', $payload);

        $successEvents = ['PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED'];

        if (! in_array($event, $successEvents, true)) {
            Log::info("Webhook Asaas legado recebido para o evento '{$event}' e foi ignorado.");

            return response()->json(['status' => 'ignored']);
        }

        $order = Order::with('user')->where('external_id', $asaasPaymentId)->first();

        if (! $order) {
            Log::warning("Webhook Asaas legado recebido para o ID {$asaasPaymentId}, mas o pedido não foi encontrado no banco.");

            return response()->json(['status' => 'not_found'], 200);
        }

        $this->marcarPedidoComoPago($order, 'Asaas legado');

        return response()->json(['status' => 'success']);
    }

    private function marcarPedidoComoPago(Order $order, string $origem): void
    {
        if ($order->status === 'pago') {
            return;
        }

        $order->update(['status' => 'pago']);
        Log::info("Pedido ID #{$order->id} alterado para PAGO via Webhook {$origem}.");

        try {
            $user = $order->user;
            if ($user) {
                Mail::send('emails.pagamento_confirmado', compact('user', 'order'), function ($message) use ($user, $order) {
                    $numeroPedido = str_pad($order->id, 5, '0', STR_PAD_LEFT);
                    $message->to($user->email)
                        ->subject("K'enzza - Pagamento Confirmado! Pedido #{$numeroPedido}");
                });
            }
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de template via Webhook: '.$e->getMessage());
        }
    }

    /**
     * Webhook capturador do Melhor Envio
     */
    public function handleMelhorEnvio(Request $request)
    {
        Log::info("Webhook Melhor Envio recebido:", $request->all());

        $shippingId = $request->input('id');

        if (!$shippingId) {
            return response()->json(['status' => 'ignorado', 'mensagem' => 'Payload vazio'], 200);
        }

        // Procura o pedido pelo ID da etiqueta que guardámos no banco
        $order = Order::where('melhor_envio_id', $shippingId)->first();

        if (!$order) {
            Log::warning("Webhook recebido para ID de frete não encontrado: " . $shippingId);
            return response()->json(['status' => 'erro', 'mensagem' => 'Pedido não localizado'], 404);
        }

        $tracking = $request->input('tracking');
        $status = $request->input('status');

        // 1. Prepara os dados de atualização
        $updateData = [];

        if ($tracking && $tracking !== $order->codigo_rastreio) {
            $updateData['codigo_rastreio'] = $tracking;
        }

        if ($status && $status !== $order->status_envio) {
             $updateData['status_envio'] = $status;
        }

        // 2. LÓGICA DE OURO: Se o status do Melhor Envio for 'delivered',
        // alteramos também o status geral do pedido para 'entregue'.
        // Isso vai disparar o OrderObserver e atualizar o BaseLinker!
        if ($status === 'delivered') {
            $updateData['status'] = 'entregue';
            Log::info("WEBHOOK: Pedido #{$order->id} identificado como 'entregue'.");
        }

        // 3. Ao atualizar, o Observer é disparado automaticamente se houver mudanças
        if (!empty($updateData)) {
             $order->update($updateData);
        }

        return response()->json(['status' => 'ok']);
    }
}
