<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class WelcomeDistributor extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Gera o link seguro para a tela de criação de senha do Laravel Breeze
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Bem-vindo(a) à K\'enzza Professional!')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Sua conta de Distribuidor Parceiro foi aprovada e criada com sucesso.')
            ->line('Para acessar seu painel exclusivo e conferir nossa tabela de preços e materiais de apoio, você precisa criar sua senha de acesso.')
            ->action('Criar Minha Senha', $url)
            ->line('Estamos muito felizes em ter você no time K\'enzza!');
            ->salutation('Atenciosamente, Equipe K\'enzza Professional.');
    }
}
