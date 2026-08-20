<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
// REMOVIDO: ShouldQueue para o e-mail disparar no ato do cadastro
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNotification extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Cria uma nova instância de notificação.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Define os canais de entrega da notificação.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Constrói a mensagem de e-mail de boas-vindas adaptativa.
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Seja bem-vindo à K\'enzza Professional!')
            ->greeting('Olá, ' . $this->user->name . '!')
            ->line('É um prazer enorme ter você conosco em nossa plataforma digital.');

        if ($this->user->role === 'salon') {
            $mail->line('Identificamos que o seu cadastro foi feito na modalidade **Compra de Salão**.')
                 ->line('Caso você tenha inserido um CNPJ ativo do segmento, sua tabela de descontos maiores já está liberada na loja.')
                 ->line('**Se você é profissional autônomo e não possui CNPJ**, lembre-se de enviar uma foto do seu Certificado Profissional para o nosso WhatsApp para que possamos validar e liberar o seu acesso especial manualmente.')
                 ->action('Acessar a Loja K\'enzza', route('shop.home'));
        } else {
            $mail->line('Sua conta para uso pessoal home care foi criada com sucesso.')
                 ->line('Agora você tem acesso direto ao nosso portfólio completo de tratamento com o padrão de excelência K\'enzza.')
                 ->action('Ir para a Loja', route('shop.home'));
        }

        $mail->line('Se precisar de qualquer auxílio com seus pedidos ou navegação, nossa equipe de suporte estará sempre à disposição.')
             ->salutation('Atenciosamente, Equipe K\'enzza Professional.');

        return $mail;
    }
}
