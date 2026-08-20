<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class AbandonedCartNotification extends Notification
{
    use Queueable;

    public $cart;

    public function __construct($cart)
    {
        $this->cart = $cart;
    }

    public function via($notifiable)
    {
        // Vai disparar por e-mail E por Push Notification no celular/PC!
        return ['mail', OneSignalChannel::class];
    }

    public function toMail($notifiable)
    {
        $url = route('cart.index');

        return (new MailMessage)
            ->subject('Você esqueceu algo no carrinho! - K\'enzza')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Vimos que você deixou alguns produtos excelentes no seu carrinho e não finalizou a compra.')
            ->line('Lembre-se: o nosso estoque de linha profissional é muito concorrido e os itens do carrinho não estão reservados.')
            ->action('Finalizar Minha Compra', $url)
            ->line('Se precisar de ajuda com o pagamento ou frete, é só chamar a nossa equipe no WhatsApp!');
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject('Seus produtos estão te esperando! 🛒')
            ->body('Volte e finalize sua compra na K\'enzza antes que o estoque acabe.')
            ->url(route('cart.index'))
            ->setParameter('include_external_user_ids', [(string) $notifiable->id])
            ->setParameter('channel_for_external_user_ids', 'push');
    }
}
