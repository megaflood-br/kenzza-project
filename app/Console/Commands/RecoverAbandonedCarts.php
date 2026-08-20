<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Notifications\AbandonedCartNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RecoverAbandonedCarts extends Command
{
    // O nome do comando que rodaremos no terminal ou no servidor
    protected $signature = 'cart:recover-abandoned';
    protected $description = 'Busca carrinhos abandonados há mais de 2 horas e envia notificações';

    public function handle()
    {
        $this->info("=== RAIO-X DO SISTEMA ===");
        $this->info("Horário interno do Laravel agora: " . Carbon::now());
        $this->info("Procurando carrinhos com updated_at ENTRE: " . Carbon::now()->subHours(3) . " E " . Carbon::now()->subHours(2));
        $this->info("-------------------------");

        // Puxa todos os carrinhos do banco para vermos o status real de cada um
        $todosCarrinhos = Cart::with('items')->get();
        foreach($todosCarrinhos as $c) {
            $userId = $c->user_id ?? 'VISITANTE (Nulo)';
            $this->info("Carrinho #{$c->id} | Usuário: {$userId} | Atualizado: {$c->updated_at} | Itens: " . $c->items->count());
        }
        $this->info("=========================\n");

        $abandonedCarts = Cart::whereNotNull('user_id')
            ->where('updated_at', '<=', Carbon::now()->subHours(2))
            ->where('updated_at', '>', Carbon::now()->subHours(3))
            ->with('user', 'items')
            ->get();

        $count = 0;

        foreach ($abandonedCarts as $cart) {
            if ($cart->items->count() > 0) {
                try {
                    $cart->user->notify(new \App\Notifications\AbandonedCartNotification($cart));
                    $count++;
                    $this->info("-> Notificação enviada para o Carrinho #{$cart->id} (Usuário {$cart->user_id})");
                } catch (\Exception $e) {
                    $this->error("Erro ao notificar carrinho #{$cart->id}: " . $e->getMessage());
                }
            } else {
                $this->warn("-> Carrinho #{$cart->id} ignorado (Não tem itens)");
            }
        }

        $this->info("\n{$count} notificações enviadas com sucesso!");
    }
}
