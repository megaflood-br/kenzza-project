<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\InfinitePayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function loginAjax(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            $user = Auth::user();

            $rua = $user->logradouro ?? $user->address ?? '';
            $cep = $user->cep ?? $user->zip_code ?? '';
            $hasAddress = ! empty($rua) && ! empty($cep);

            return response()->json([
                'sucesso' => true,
                'csrf' => csrf_token(),
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'has_address' => $hasAddress,
                    'logradouro' => $rua,
                    'numero' => $user->numero ?? $user->number ?? '',
                    'bairro' => $user->neighborhood ?? $user->bairro ?? '',
                    'cidade' => $user->cidade ?? $user->city ?? '',
                    'estado' => $user->estado ?? $user->state ?? '',
                    'cep' => preg_replace('/\D/', '', $cep),
                ],
            ]);
        }

        return response()->json(['sucesso' => false, 'erro' => 'As credenciais informadas estão incorretas.'], 401);
    }

    public function salvarEnderecoAjax(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['sucesso' => false, 'erro' => 'Não autorizado.'], 401);
        }

        if ($request->has('cep')) {
            $request->merge(['cep' => preg_replace('/\D/', '', $request->cep)]);
        }

        $dados = $request->validate([
            'cep' => 'required|string|digits:8',
            'logradouro' => 'required|string',
            'numero' => 'required|string',
            'bairro' => 'required|string',
            'cidade' => 'required|string',
            'estado' => 'required|string|size:2',
        ]);

        $user = Auth::user();

        $user->update([
            'cep' => $dados['cep'], 'zip_code' => $dados['cep'],
            'logradouro' => $dados['logradouro'], 'address' => $dados['logradouro'],
            'numero' => $dados['numero'], 'number' => $dados['numero'],
            'neighborhood' => $dados['bairro'], 'bairro' => $dados['bairro'],
            'cidade' => $dados['cidade'], 'city' => $dados['cidade'],
            'estado' => strtoupper($dados['estado']), 'state' => strtoupper($dados['estado']),
        ]);

        return response()->json(['sucesso' => true, 'mensagem' => 'Endereço atualizado com sucesso!']);
    }

    public function checkout(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('cart.index')->with('error', 'Por favor, faça login para finalizar.');
        }

        $carrinho = session('cart', []);
        if (empty($carrinho)) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        $user = Auth::user();
        if (empty($user->cep) && empty($user->zip_code)) {
            return redirect()->route('cart.index')->with('error', 'Por favor, cadastre seu endereço antes de finalizar.');
        }

        $frete = floatval($request->input('shipping_value', 15.00));
        session()->put('checkout_shipping_value', $frete);
        session()->put('checkout_shipping_service_id', $request->input('shipping_service_id', 1));

        $metodoEscolhido = $request->input('payment_method', 'pix');

        if (in_array($metodoEscolhido, ['cartao', 'card'], true)) {
            return $this->processPayment('cartao');
        }

        return $this->paymentView();
    }

    /**
     * Cartão: cria o pedido e redireciona para o checkout hospedado da InfinitePay.
     */
    public function processPayment($method)
    {
        if (! Auth::check()) {
            return redirect()->route('cart.index');
        }

        try {
            $order = $this->criarPedidoPendente('cartao');
            $user = Auth::user();

            $this->enviarEmailPedidoCriado($user, $order, "K'enzza - Pedido Recebido! (Aguardando Pagamento)");

            session()->forget(['cart', 'coupon', 'checkout_shipping_value', 'checkout_shipping_service_id']);

            return redirect()->away(InfinitePayService::checkoutUrl($order, $user));
        } catch (\Exception $e) {
            Log::error('Erro Checkout InfinitePay Cartão: '.$e->getMessage());

            return redirect()->route('cart.index')->with('error', 'Erro ao processar o pagamento: '.$e->getMessage());
        }
    }

    /**
     * PIX: gera QR Code local (chave PIX da loja) e aguarda confirmação manual / conciliação.
     */
    public function paymentView()
    {
        if (! Auth::check()) {
            return redirect()->route('cart.index');
        }

        if (session('checkout_shipping_value') === null) {
            session()->put('checkout_shipping_value', 15.00);
            session()->put('checkout_shipping_service_id', 1);
        }

        $order = Order::where('user_id', Auth::id())
            ->where('status', 'pendente')
            ->where('metodo_pagamento', 'pix_manual')
            ->latest()
            ->first();

        try {
            if (! $order) {
                $order = $this->criarPedidoPendente('pix_manual');
                $user = Auth::user();
                $this->enviarEmailPedidoCriado($user, $order, "K'enzza - Aguardando Pagamento via PIX");
            }

            $pixKey = trim((string) env('PIX_KEY', ''));
            if ($pixKey === '') {
                throw new \Exception('Chave PIX (PIX_KEY) não configurada no ambiente.');
            }

            $pixPayload = trim($this->gerarPayloadPix($pixKey, "Kenzza Professional", 'Atibaia', $order->total, $order->id));
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PIX InfinitePay/manual: '.$e->getMessage());

            return redirect()->route('cart.index')->with('error', 'Erro no Pagamento: '.$e->getMessage());
        }

        $totalFinal = $order->total;

        return view('ecommerce.payment', compact('totalFinal', 'pixPayload', 'order'));
    }

    public function confirmPix(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('cart.index');
        }

        $orderId = $request->input('order_id');
        $order = Order::where('user_id', Auth::id())->find($orderId);

        if (! $order) {
            return redirect()->route('cart.index')->with('error', 'Pedido de referência não localizado.');
        }

        $order->update(['status' => 'aguardando_confirmacao']);
        session()->forget(['cart', 'coupon', 'checkout_shipping_value', 'checkout_shipping_service_id']);

        return redirect()->route('shop.home')->with('success', 'Recebemos seu aviso! Após confirmarmos o PIX, seu pedido será liberado.');
    }

    private function criarPedidoPendente(string $metodo): Order
    {
        $carrinho = session('cart', []);
        $coupon = session('coupon');

        if (empty($carrinho)) {
            throw new \Exception('Carrinho expirado ou vazio.');
        }

        DB::beginTransaction();

        try {
            $subtotal = 0;
            foreach ($carrinho as $id => $item) {
                $produto = Product::findOrFail($id);
                if ($item['quantidade'] > $produto->estoque) {
                    throw new \Exception("O produto {$produto->nome} não possui estoque suficiente.");
                }
                $subtotal += ($produto->preco_atual * (int) $item['quantidade']);
            }

            $desconto = ($coupon && isset($coupon['tipo']) && $coupon['tipo'] === 'percentual')
                ? ($subtotal * ($coupon['valor'] / 100))
                : 0;

            $frete = session('checkout_shipping_value') !== null
                ? floatval(session('checkout_shipping_value'))
                : 15.00;

            if ($coupon && isset($coupon['tipo']) && $coupon['tipo'] === 'frete_gratis') {
                $frete = 0;
            }

            $totalFinal = ($subtotal - $desconto) + $frete;
            $externalRef = 'KENZZA-'.time();

            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $totalFinal,
                'status' => 'pendente',
                'external_id' => $externalRef,
                'frete' => $frete,
                'metodo_pagamento' => $metodo,
                'shipping_service_id' => session('checkout_shipping_service_id', 1),
            ]);

            foreach ($carrinho as $id => $item) {
                $produto = Product::findOrFail($id);
                $subtotalItem = $produto->preco_atual * (int) $item['quantidade'];

                $order->items()->create([
                    'product_id' => $id,
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $produto->preco_atual,
                    'subtotal' => $subtotalItem,
                ]);

                $produto->decrement('estoque', (int) $item['quantidade']);
            }

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function enviarEmailPedidoCriado($user, Order $order, string $assunto): void
    {
        try {
            Mail::send('emails.pedido_criado', compact('user', 'order'), function ($message) use ($user, $assunto) {
                $message->to($user->email)->subject($assunto);
            });
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de pedido criado: '.$e->getMessage());
        }
    }

    private function gerarPayloadPix($key, $name, $city, $amount, $txid)
    {
        $key = preg_replace('/[^a-zA-Z0-9@.\-_]/', '', $key);
        if (preg_match('/^[0-9]{10,11}$/', $key)) {
            $key = '+55'.$key;
        }

        $name = substr(preg_replace('/[^a-zA-Z0-9 ]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $name)), 0, 25);
        $city = substr(preg_replace('/[^a-zA-Z0-9 ]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $city)), 0, 15);

        $txid = substr(preg_replace('/[^a-zA-Z0-9]/', '', $txid), 0, 25);
        if (empty($txid)) {
            $txid = 'KENZZA';
        }

        $amount = number_format((float) $amount, 2, '.', '');

        $blocoChave = '0014br.gov.bcb.pix01'.str_pad(strlen($key), 2, '0', STR_PAD_LEFT).$key;
        $campo26 = '26'.str_pad(strlen($blocoChave), 2, '0', STR_PAD_LEFT).$blocoChave;
        $campo54 = '54'.str_pad(strlen($amount), 2, '0', STR_PAD_LEFT).$amount;
        $blocoTxId = '05'.str_pad(strlen($txid), 2, '0', STR_PAD_LEFT).$txid;
        $campo62 = '62'.str_pad(strlen($blocoTxId), 2, '0', STR_PAD_LEFT).$blocoTxId;

        $payload = '000201'.$campo26.'520400005303986'.$campo54.'5802BR'.
                   '59'.str_pad(strlen($name), 2, '0', STR_PAD_LEFT).$name.
                   '60'.str_pad(strlen($city), 2, '0', STR_PAD_LEFT).$city.
                   $campo62.'6304';

        return $payload.$this->crc16($payload);
    }

    private function crc16($payload)
    {
        $crc = 0xFFFF;
        $poly = 0x1021;
        for ($i = 0; $i < strlen($payload); $i++) {
            $crc ^= (ord($payload[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ $poly) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
