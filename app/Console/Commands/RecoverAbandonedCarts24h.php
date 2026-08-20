<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Models\Coupon;
use App\Notifications\AbandonedCartDiscountNotification;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RecoverAbandonedCarts24h extends Command
{
    protected $signature = 'cart:recover-abandoned-24h';
    protected $description = 'Busca carrinhos abandonados há 24h e envia cupom de desconto exclusivo';

    public function handle()
    {
        // Busca carrinhos atualizados entre 24 e 25 horas atrás
        $abandonedCarts = Cart::whereNotNull('user_id')
            ->where('updated_at', '<=', Carbon::now()->subHours(24))
            ->where('updated_at', '>', Carbon::now()->subHours(25))
            ->with('user', 'items')
            ->get();

        $count = 0;

        foreach ($abandonedCarts as $cart) {
            // Só notifica se ainda tiver itens (pode ser que ele tenha removido tudo do carrinho)
            if ($cart->items->count() > 0) {
                try {
                    // Cria um cupom EXCLUSIVO (Ex: VOLTA-A7B2-65)
                    $couponCode = 'VOLTA-' . strtoupper(Str::random(4)) . '-' . $cart->id;

                    Coupon::create([
                        'codigo' => $couponCode,
                        'tipo' => 'percentual',
                        'valor' => 5.00,
                        'limite_uso' => 1,
                        'vezes_usado' => 0,
                        'ativo' => true,
                        'validade' => Carbon::now()->addDays(2) // Expira em exatas 48 horas
                    ]);

                    // Envia a notificação passando o carrinho e o código do cupom gerado
                    $cart->user->notify(new AbandonedCartDiscountNotification($cart, $couponCode));
                    $count++;

                } catch (\Exception $e) {
                    Log::error("Erro ao notificar (24h) carrinho do usuário {$cart->user_id}: " . $e->getMessage());
                }
            }
        }

        $this->info("{$count} cupons de abandono (24h) enviados com sucesso!");
    }
}
