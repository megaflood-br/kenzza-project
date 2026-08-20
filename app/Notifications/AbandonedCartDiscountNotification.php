<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class AbandonedCartDiscountNotification extends Notification
{
    use Queueable;

    public $cart;
    public $couponCode;

    public function __construct($cart, $couponCode)
    {
        $this->cart = $cart;
        $this->couponCode = $couponCode;
    }

    public function via($notifiable)
    {
        return ['mail', OneSignalChannel::class];
    }

    public function toMail($notifiable)
    {
        $url = route('cart.index');

        return (new MailMessage)
            ->subject('Liberamos 5% OFF para você fechar seu pedido! 🎁')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Seus produtos da K\'enzza continuam guardados no carrinho, mas queremos dar um empurrãozinho especial para você levar a melhor qualidade profissional para casa.')
            ->line('Utilize o cupom abaixo no seu carrinho para garantir **5% de desconto** em toda a sua compra:')
            ->line('**CUPOM: ' . $this->couponCode . '**')
            ->line('⚠️ **Atenção:** Este cupom foi gerado exclusivamente para você e vai expirar automaticamente em 48 horas!')
            ->action('Garantir Meu Desconto', $url)
            ->line('Se ficou com alguma dúvida sobre os produtos, nossa equipe está pronta para ajudar no WhatsApp.');
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject('Presente pra você! 🎁 5% OFF')
            ->body('Use o cupom ' . $this->couponCode . ' no carrinho. Corre que expira em 48h!')
            ->url(route('cart.index'))
            ->setParameter('include_external_user_ids', [(string) $notifiable->id])
            ->setParameter('channel_for_external_user_ids', 'push');
    }
}
