<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KenzzaResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Redefinição de Senha - K\'enzza Professional')
            ->greeting('Olá!')
            ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para a sua conta.')
            ->action('Redefinir Senha', url(config('app.url').route('password.reset', $this->token, false)))
            ->line('Este link de redefinição de senha expirará em 60 minutos.')
            ->line('Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária.')
            ->salutation('Atenciosamente, Equipe K\'enzza Professional.');
    }
}
