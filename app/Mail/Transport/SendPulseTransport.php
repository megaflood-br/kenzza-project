<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class SendPulseTransport extends AbstractTransport
{
    protected string $clientId;
    protected string $clientSecret;

    public function __construct(string $clientId, string $clientSecret)
    {
        parent::__construct();
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        // O SendPulse exige um token que dura 1 hora. O Cache memoriza isso para o site não ficar lento.
        $token = Cache::remember('sendpulse_token', 3500, function () {
            $response = Http::post('https://api.sendpulse.com/oauth/access_token', [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);
            return $response->json('access_token');
        });

        $html = $email->getHtmlBody();
        $text = $email->getTextBody();
        $subject = $email->getSubject();

        $from = $email->getFrom()[0];
        $sender = [
            'name'  => $from->getName() ?: config('app.name'),
            'email' => $from->getAddress()
        ];

        $to = [];
        foreach ($email->getTo() as $recipient) {
            $to[] = [
                'name'  => $recipient->getName() ?: '',
                'email' => $recipient->getAddress()
            ];
        }

        $payload = [
            'email' => [
                'html'    => base64_encode($html ?: ($text ?: ' ')),
                'text'    => $text ?: ' ',
                'subject' => $subject,
                'from'    => $sender,
                'to'      => $to,
            ]
        ];

        // Dispara o e-mail via API contornando qualquer bloqueio de porta
        Http::withToken($token)->post('https://api.sendpulse.com/smtp/emails', $payload);
    }

    public function __toString(): string
    {
        return 'sendpulse';
    }
}
