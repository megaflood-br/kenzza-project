<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOrderToContaAzul implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
{
    // Hoje: Apenas registra que o pedido está pronto para integrar
    \Log::info("Pedido #{$this->order->id} pronto para integração com Conta Azul.");

    // Futuramente: Aqui entrará o código da API do Conta Azul
}
}
