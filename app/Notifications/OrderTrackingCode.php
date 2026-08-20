<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// Importações necessárias para o OneSignal funcionar
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class OrderTrackingCode extends Notification
{
    use Queueable;
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        // Agora dispara e-mail E notificação push no celular
        return ['mail', OneSignalChannel::class];
    }

    public function toMail($notifiable)
    {
        $url = url('/minha-conta/painel');

        return (new MailMessage)
            ->subject('Seu pedido foi enviado! Rastreio disponível - K\'enzza')
            ->view('emails.order-tracking', [
                'user' => $notifiable,
                'order' => $this->order,
                'url' => $url
            ]);
    }

    public function toOneSignal($notifiable)
    {
        // Montando o Push Notification com o código de rastreio direto na tela do cliente
        return OneSignalMessage::create()
            ->subject("Pedido Enviado! 🚚")
            ->body("Olá, " . $notifiable->name . ". O pedido #" . str_pad($this->order->id, 5, '0', STR_PAD_LEFT) . " foi despachado. Rastreio: " . $this->order->codigo_rastreio)
            ->url(url('/minha-conta/painel')) // Se o cliente clicar na notificação, abre o painel
            ->setParameter('include_external_user_ids', [(string) $notifiable->id])
            ->setParameter('channel_for_external_user_ids', 'push'); // A correção da API aqui
    }
}
