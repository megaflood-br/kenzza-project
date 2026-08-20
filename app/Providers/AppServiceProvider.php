<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade; // <-- IMPORTANTE: Adicionado para registrar componentes
use Illuminate\Support\Facades\URL;   // <-- IMPORTANTE: Adicionado para forçar HTTPS em produção
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;               // <-- IMPORTANTE
use App\Models\Product;            // <-- IMPORTANTE
use App\Models\Coupon;             // <-- Adicione os outros Models que quer logar aqui
use App\Observers\AuditObserver;   // <-- IMPORTANTE
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Berkayk\OneSignal\OneSignalChannel;
use App\Observers\OrderObserver;
use App\Mail\Transport\SendPulseTransport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Força o uso de HTTPS em ambiente de produção (Hostinger)
         * Resolve loops de redirecionamento no primeiro clique de verificação de e-mail
         */
        if (config('app.env') === 'production' || app()->environment('production')) {
            URL::forceScheme('https');
        }

        Mail::extend('sendpulse', function (array $config = []) {
            return new SendPulseTransport(
                $config['client_id'],
                $config['client_secret']
            );
        });
        /**
         * REGISTRO DOS OBSERVERS DE LOG DE AUDITORIA
         * Adicione qualquer Model da K'enzza que você queira rastrear ações
         */
        User::observe(AuditObserver::class);
        Product::observe(AuditObserver::class);
        Order::observe(AuditObserver::class);
        Ticket::observe(AuditObserver::class);
        // Ex: Coupon::observe(AuditObserver::class);
        /**
         * View Composer para o Menu de Navegação
         * Garante que as contagens de notificações apareçam apenas para Admin/Editor
         */
        View::composer('layouts.navigation', function ($view) {
            $pendingOrdersCount = 0;
            $openTicketsCount = 0;

            if (Auth::check()) {
                $user = Auth::user();

                if (in_array($user->role, ['admin', 'editor'])) {
                    $pendingOrdersCount = Order::where('status', 'pendente')->count();
                    $openTicketsCount = Ticket::where('status', 'open')->count();
                }
            }

            $view->with([
                'pendingOrdersCount' => $pendingOrdersCount,
                'openTicketsCount' => $openTicketsCount
            ]);
        });

        /**
         * REGISTRO DO DRIVER ONESIGNAL
         */
        Notification::extend('onesignal', function ($app) {
            return $app->make(OneSignalChannel::class);
        });

        /**
         * REGISTRO DO COMPONENTE DE LAYOUT DA LOJA
         * Isso permite que você use <x-store-layout> em suas views
         */
        Blade::component('layouts.store', 'store-layout');

        Order::observe(OrderObserver::class);
    }
}
