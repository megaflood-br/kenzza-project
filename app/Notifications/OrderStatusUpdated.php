<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class OrderStatusUpdated extends Notification
{
    use Queueable;
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', OneSignalChannel::class];
    }

    public function toMail($notifiable)
    {
        $statusFormatado = mb_strtoupper(str_replace('_', ' ', $this->order->status), 'UTF-8');
        $url = url('/minha-conta/painel');

        return (new MailMessage)
            ->subject('Atualização do Pedido #' . str_pad($this->order->id, 5, '0', STR_PAD_LEFT) . ' - K\'enzza')
            ->view('emails.order-status', [
                'user' => $notifiable,
                'order' => $this->order,
                'statusFormatado' => $statusFormatado,
                'url' => $url
            ]);
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject("Pedido #" . str_pad($this->order->id, 5, '0', STR_PAD_LEFT))
            ->body("Olá, " . $notifiable->name . ". Seu pedido teve o status atualizado para: " . mb_strtoupper(str_replace('_', ' ', $this->order->status), 'UTF-8'))
            ->setParameter('include_external_user_ids', [(string) $notifiable->id])
            ->setParameter('channel_for_external_user_ids', 'push'); // <--- A MÁGICA ACONTECE AQUI
    }
}
