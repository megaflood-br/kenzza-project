<div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px 30px; border: 1px solid #eaeaea; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">

    <div style="text-align: center; border-bottom: 1px solid #f0f0f0; padding-bottom: 30px; margin-bottom: 30px;">
        <a href="{{ config('app.url') }}" style="display: inline-block; text-decoration: none;">
            <img src="https://kenzza.com.br/logo-k-preto.png"
                 alt="K'enzza Professional"
                 style="width: 160px; height: auto; border: none;">
        </a>
    </div>

    <div style="color: #333333; font-size: 16px; line-height: 1.6;">
        <p style="font-size: 18px; font-weight: 600; color: #000000; margin-top: 0;">Ótima notícia, {{ $user->name }}!</p>

        <p>A etiqueta do seu pedido <strong>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</strong> foi gerada e ele já está a caminho do seu endereço.</p>

        <div style="background-color: #000000; text-align: center; padding: 25px 20px; margin: 30px 0; border-radius: 8px;">
            <p style="margin: 0; font-size: 12px; color: #aaaaaa; text-transform: uppercase; letter-spacing: 1px;">Código de Rastreamento</p>
            <p style="margin: 10px 0 0 0; font-size: 24px; font-weight: bold; color: #B8860B; letter-spacing: 2px;">{{ $order->codigo_rastreio }}</p>
        </div>

        <p style="font-size: 14px;">Você pode utilizar este código no site da transportadora escolhida para acompanhar cada passo da entrega.</p>

        <div style="text-align: center; margin: 40px 0;">
            <a href="{{ $url }}" style="background-color: #ffffff; color: #000000; border: 2px solid #000000; padding: 14px 32px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 5px; text-transform: uppercase; letter-spacing: 1px; display: inline-block;">
                Acompanhar no Painel
            </a>
        </div>
    </div>

    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #f0f0f0; text-align: center;">
        <p style="font-size: 12px; color: #999999; line-height: 1.5; margin-bottom: 15px;">
            Se o botão não funcionar, copie e cole este link no seu navegador:<br>
            <a href="{{ $url }}" style="color: #B8860B; word-break: break-all; text-decoration: none;">{{ $url }}</a>
        </p>
        <p style="font-size: 12px; color: #aaaaaa; margin: 0;">
            &copy; {{ date('Y') }} K'enzza Professional. Todos os direitos reservados.
        </p>
    </div>
</div>
