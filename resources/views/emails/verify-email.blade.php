<div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px 30px; border: 1px solid #eaeaea; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">

    <div style="text-align: center; border-bottom: 1px solid #f0f0f0; padding-bottom: 30px; margin-bottom: 30px;">
        <a href="{{ config('app.url') }}" style="display: inline-block; text-decoration: none;">
            <img src="https://kenzza.com.br/logo-k-preto.png"
                 alt="K'enzza Professional"
                 style="width: 160px; height: auto; border: none;">
        </a>
    </div>

    <div style="color: #333333; font-size: 16px; line-height: 1.6;">
        <p style="font-size: 18px; font-weight: 600; color: #000000; margin-top: 0;">Olá, {{ $user->name }},</p>

        <p>Seja bem-vindo(a) ao universo <strong>K'enzza Professional</strong>.</p>

        <p>Para garantir a segurança da sua conta e liberar seu acesso completo à nossa plataforma, por favor, confirme seu endereço de e-mail clicando no botão abaixo:</p>

        <div style="text-align: center; margin: 45px 0;">
            <a href="{{ $url }}" style="background-color: #000000; color: #B8860B; padding: 16px 36px; text-decoration: none; font-size: 15px; font-weight: bold; border-radius: 5px; text-transform: uppercase; letter-spacing: 1px; display: inline-block;">
                Confirmar meu e-mail
            </a>
        </div>

        <p style="font-size: 14px; color: #666666;">
            Se você não iniciou este cadastro, por favor, desconsidere este e-mail. Nenhuma ação adicional é necessária.
        </p>
    </div>

    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #f0f0f0; text-align: center;">
        <p style="font-size: 12px; color: #999999; line-height: 1.5; margin-bottom: 15px;">
            Se estiver com dificuldades para clicar no botão, copie e cole o link abaixo diretamente no seu navegador:<br>
            <a href="{{ $url }}" style="color: #B8860B; word-break: break-all; text-decoration: none;">{{ $url }}</a>
        </p>
        <p style="font-size: 12px; color: #aaaaaa; margin: 0;">
            &copy; {{ date('Y') }} K'enzza Professional. Todos os direitos reservados.
        </p>
    </div>

</div>
