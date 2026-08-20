<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{
    /**
     * Webhook unificado do Asaas (Monitora PIX e Cartão)
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Webhook Asaas Recebido:', $payload);

        $asaasPaymentId = $payload['payment']['id'] ?? null;
        $event = $payload['event'] ?? null;

        if (!$asaasPaymentId) {
            return response()->json(['status' => 'ignored', 'message' => 'Sem ID de pagamento'], 200);
        }

        $successEvents = ['PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED'];

        if (in_array($event, $successEvents)) {

            $order = Order::with('user')->where('external_id', $asaasPaymentId)->first();

            if ($order) {
                if ($order->status !== 'pago') {
                    $order->update([
                        'status' => 'pago'
                    ]);
                    Log::info("Pedido ID #{$order->id} alterado para PAGO via Webhook Asaas. Evento: {$event}");

                    // Enviar e-mail automático utilizando o template Blade da pasta views/emails
                    try {
                        $user = $order->user;
                        if ($user) {
                            Mail::send('emails.pagamento_confirmado', compact('user', 'order'), function ($message) use ($user, $order) {
                                // Usamos o ID formatado no assunto para ficar profissional
                                $numeroPedido = str_pad($order->id, 5, '0', STR_PAD_LEFT);
                                $message->to($user->email)
                                        ->subject("K'enzza - Pagamento Confirmado! Pedido #{$numeroPedido}");
                            });
                        }
                    } catch (\Exception $e) {
                        Log::error("Erro ao enviar e-mail de template via Webhook: " . $e->getMessage());
                    }
                }
                return response()->json(['status' => 'success']);
            }

            Log::warning("Webhook Asaas recebido para o ID {$asaasPaymentId}, mas o pedido não foi encontrado no banco.");
            return response()->json(['status' => 'not_found'], 200);
        }

        Log::info("Webhook Asaas recebido para o evento '{$event}' e foi ignorado pelo sistema.");
        return response()->json(['status' => 'ignored']);
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
