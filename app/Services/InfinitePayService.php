<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class InfinitePayService
{
    /**
     * Monta o link de checkout hospedado da InfinitePay (pay.infinitepay.io),
     * o mesmo formato usado em produção antes da migração para o Asaas.
     */
    public static function checkoutUrl(Order $order, ?User $user = null): string
    {
        $handle = trim((string) env('INFINITEPAY_HANDLE', 'kenzza'));
        $valorFormatado = number_format((float) $order->total, 2, ',', '');
        $user = $user ?? $order->user;

        $params = [
            'order_id' => $order->external_id,
            'redirect_url' => route('shop.obrigado'),
        ];

        if ($user) {
            $cpf = preg_replace('/\D/', '', (string) ($user->cpf ?? $user->document ?? ''));
            if ($cpf !== '') {
                $params['cd'] = $cpf;
            }
            if (! empty($user->email)) {
                $params['email'] = $user->email;
            }
        }

        return 'https://pay.infinitepay.io/'.$handle.'/'.$valorFormatado.'?'.http_build_query($params);
    }

    /**
     * Extrai o identificador do pedido no payload do webhook da InfinitePay.
     */
    public static function orderReferenceFromPayload(array $payload): ?string
    {
        $candidates = [
            $payload['order_id'] ?? null,
            $payload['order_nsu'] ?? null,
            $payload['reference_id'] ?? null,
            $payload['data']['order_id'] ?? null,
            $payload['data']['order_nsu'] ?? null,
        ];

        foreach ($candidates as $value) {
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * Status de sucesso usados historicamente pelo webhook da InfinitePay.
     */
    public static function isPaidStatus(?string $status): bool
    {
        return in_array(strtolower((string) $status), ['approved', 'paid', 'confirmed', 'succeeded'], true);
    }
}
