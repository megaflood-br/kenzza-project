<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    private $asaasUrl;
    private $asaasKey;

    public function __construct()
    {
        $this->asaasKey = env('ASAAS_API_KEY');
        $this->asaasUrl = env('ASAAS_ENV') === 'production'
            ? 'https://www.asaas.com/api/v3'
            : 'https://sandbox.asaas.com/api/v3';
    }

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
            $hasAddress = !empty($rua) && !empty($cep);

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
                    'cep' => preg_replace('/\D/', '', $cep)
                ]
            ]);
        }

        return response()->json(['sucesso' => false, 'erro' => 'As credenciais informadas estão incorretas.'], 401);
    }

    public function salvarEnderecoAjax(Request $request)
    {
        if (!Auth::check()) {
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
        if (!Auth::check()) {
            return redirect()->route('cart.index')->with('error', 'Por favor, faça login para finalizar.');
        }

        $carrinho = session('cart', []);
        if (empty($carrinho)) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        $user = Auth::user();
        if (empty($user->cep) && empty($user->zip_code)) {
            $user->update([
                'cep' => '12951110', 'zip_code' => '12951110',
                'logradouro' => 'Rua Machado de Assis', 'numero' => '465',
                'bairro' => 'Jardim das Cerejeiras', 'cidade' => 'Atibaia', 'estado' => 'SP'
            ]);
            $user->refresh();
        }

        $frete = floatval($request->input('shipping_value', 15.00));
        session()->put('checkout_shipping_value', $frete);
        session()->put('checkout_shipping_service_id', $request->input('shipping_service_id', 1));

        $metodoEscolhido = $request->input('payment_method', 'pix');

        if ($metodoEscolhido === 'cartao') {
            return $this->processPayment('cartao');
        }

        return $this->paymentView();
    }

    public function processPayment($method)
    {
        if (!Auth::check()) return redirect()->route('cart.index');

        $carrinho = session('cart', []);
        $coupon = session('coupon');

        if (empty($carrinho)) {
            return redirect()->route('cart.index')->with('error', 'Carrinho expirado ou vazio.');
        }

        try {
            DB::beginTransaction();

            $subtotal = 0;
            foreach ($carrinho as $id => $item) {
                $produto = Product::findOrFail($id);
                if ($item['quantidade'] > $produto->estoque) {
                    return redirect()->route('cart.index')->with('error', "O produto {$produto->nome} não possui estoque suficiente.");
                }
                $subtotal += ($produto->preco_atual * (int)$item['quantidade']);
            }

            $desconto = ($coupon && isset($coupon['tipo']) && $coupon['tipo'] === 'percentual') ? ($subtotal * ($coupon['valor'] / 100)) : 0;
            $frete = session('checkout_shipping_value') !== null ? floatval(session('checkout_shipping_value')) : 15.00;
            if ($coupon && isset($coupon['tipo']) && $coupon['tipo'] === 'frete_gratis') {
                $frete = 0;
            }

            $totalFinal = ($subtotal - $desconto) + $frete;
            $externalRef = 'KENZZA-' . time();

            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $totalFinal,
                'status' => 'pendente',
                'external_id' => $externalRef,
                'frete' => $frete,
                'metodo_pagamento' => 'cartao',
                'shipping_service_id' => session('checkout_shipping_service_id', 1),
            ]);

            foreach ($carrinho as $id => $item) {
                $produto = Product::findOrFail($id);
                $subtotalItem = $produto->preco_atual * (int)$item['quantidade'];

                $order->items()->create([
                    'product_id'     => $id,
                    'quantidade'     => $item['quantidade'],
                    'preco_unitario' => $produto->preco_atual,
                    'subtotal'       => $subtotalItem,
                ]);

                $produto->decrement('estoque', (int)$item['quantidade']);
            }

            $user = Auth::user();

            // --- INÍCIO DA LÓGICA DE PARCELAMENTO COM JUROS ---
            $parcelas = (int) request('installments', 1);
            $totalAsaas = $order->total;

            // Se for maior que 3 parcelas, aplica juros de 2.99% ao mês (Tabela Price)
            if ($parcelas > 3) {
                $taxaDeJuros = 0.0299; // 2.99%
                $valorDaParcela = $totalAsaas * ($taxaDeJuros * pow(1 + $taxaDeJuros, $parcelas)) / (pow(1 + $taxaDeJuros, $parcelas) - 1);
                $totalAsaas = round($valorDaParcela * $parcelas, 2);

                // Atualiza o valor do pedido no seu banco para refletir o total com juros cobrado do cliente
                $order->update(['total' => $totalAsaas]);
            }

            $payloadAsaas = [
                'customer' => $this->getOrCreateAsaasCustomer($user),
                'billingType' => 'CREDIT_CARD',
                'dueDate' => now()->addDays(1)->format('Y-m-d'),
                'externalReference' => $externalRef,
                'callback' => [
                    'successUrl' => route('shop.obrigado'),
                    'autoRedirect' => true,
                ]
            ];

            if ($parcelas > 1) {
                $payloadAsaas['installmentCount'] = $parcelas;
                $payloadAsaas['totalValue'] = floatval($totalAsaas);
                $endpoint = '/installments';
            } else {
                $payloadAsaas['value'] = floatval($totalAsaas);
                $endpoint = '/payments';
            }

            $response = Http::withHeaders([
                'access_token' => $this->asaasKey,
                'Content-Type' => 'application/json'
            ])->post($this->asaasUrl . $endpoint, $payloadAsaas);

            if ($response->failed()) {
                throw new \Exception('Erro na API do Asaas ao criar cobrança: ' . $response->body());
            }

            $asaasData = $response->json();

            // Se for parcelado, buscamos a 1ª parcela gerada pelo Asaas para extrair o Link de Pagamento (invoiceUrl)
            if ($parcelas > 1) {
                $installmentId = $asaasData['id'];

                $paymentsResponse = Http::withHeaders([
                    'access_token' => $this->asaasKey
                ])->get($this->asaasUrl . "/installments/{$installmentId}/payments");

                if ($paymentsResponse->failed() || empty($paymentsResponse->json()['data'])) {
                    throw new \Exception('Erro ao buscar as parcelas geradas no Asaas.');
                }

                $asaasData = $paymentsResponse->json()['data'][0];
            }
            // --- FIM DA LÓGICA DE PARCELAMENTO ---

            $order->update(['external_id' => $asaasData['id']]);

            DB::commit();

            try {
                Mail::send('emails.pedido_criado', compact('user', 'order'), function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject("K'enzza - Pedido Recebido! (Aguardando Pagamento)");
                });
            } catch (\Exception $e) {
                Log::error("Erro ao enviar e-mail de template (Cartão): " . $e->getMessage());
            }

            session()->forget(['cart', 'coupon', 'checkout_shipping_value', 'checkout_shipping_service_id']);

            return redirect()->away($asaasData['invoiceUrl']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro Checkout Asaas Cartão: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Erro Cartão: ' . $e->getMessage());
        }
    }

    public function paymentView()
    {
        if (!Auth::check()) return redirect()->route('cart.index');

        if (session('checkout_shipping_value') === null) {
            session()->put('checkout_shipping_value', 15.00);
            session()->put('checkout_shipping_service_id', 1);
        }

        $order = Order::where('user_id', Auth::id())
                      ->where('status', 'pendente')
                      ->where('metodo_pagamento', 'pix')
                      ->latest()
                      ->first();

        $pixPayload = '';
        $asaasPaymentId = '';

        try {
            if (!$order) {
                $carrinho = session('cart', []);
                if (empty($carrinho)) {
                    return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
                }

                DB::beginTransaction();

                $subtotal = 0;
                foreach ($carrinho as $id => $item) {
                    $produto = Product::findOrFail($id);
                    if ($item['quantidade'] > $produto->estoque) {
                        return redirect()->route('cart.index')->with('error', "Estoque insuficiente para o item {$produto->nome}.");
                    }
                    $subtotal += ($produto->preco_atual * (int)$item['quantidade']);
                }

                $coupon = session('coupon');
                $desconto = ($coupon && isset($coupon['tipo']) && $coupon['tipo'] === 'percentual') ? ($subtotal * ($coupon['valor'] / 100)) : 0;
                $frete = floatval(session('checkout_shipping_value', 15.00));

                $totalFinal = ($subtotal - $desconto) + $frete;
                $externalRef = 'KENZZA-' . time();

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'total' => $totalFinal,
                    'status' => 'pendente',
                    'external_id' => $externalRef,
                    'frete' => $frete,
                    'metodo_pagamento' => 'pix',
                    'shipping_service_id' => session('checkout_shipping_service_id', 1),
                ]);

                foreach ($carrinho as $id => $item) {
                    $produto = Product::findOrFail($id);
                    $subtotalItem = $produto->preco_atual * (int)$item['quantidade'];

                    $order->items()->create([
                        'product_id'     => $id,
                        'quantidade'     => $item['quantidade'],
                        'preco_unitario' => $produto->preco_atual,
                        'subtotal'       => $subtotalItem,
                    ]);

                    $produto->decrement('estoque', (int)$item['quantidade']);
                }

                $user = Auth::user();
                $response = Http::withHeaders([
                    'access_token' => $this->asaasKey,
                    'Content-Type' => 'application/json'
                ])->post($this->asaasUrl . '/payments', [
                    'customer' => $this->getOrCreateAsaasCustomer($user),
                    'billingType' => 'PIX',
                    'value' => floatval($order->total),
                    'dueDate' => now()->format('Y-m-d'),
                    'externalReference' => $externalRef,
                ]);

                if ($response->failed()) {
                    throw new \Exception('Erro na API do Asaas ao criar PIX: ' . $response->body());
                }

                $asaasData = $response->json();
                $asaasPaymentId = $asaasData['id'];

                $order->update(['external_id' => $asaasPaymentId]);

                DB::commit();

                try {
                    Mail::send('emails.pedido_criado', compact('user', 'order'), function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject("K'enzza - Aguardando Pagamento via PIX");
                    });
                } catch (\Exception $e) {
                    Log::error("Erro ao enviar e-mail de template (PIX): " . $e->getMessage());
                }
            } else {
                $asaasPaymentId = $order->external_id;
            }

            $pixResponse = Http::withHeaders([
                'access_token' => $this->asaasKey
            ])->get($this->asaasUrl . "/payments/{$asaasPaymentId}/pixQrCode");

            if ($pixResponse->failed()) {
                throw new \Exception('Erro ao buscar QR Code Pix na API do Asaas: ' . $pixResponse->body());
            }

            $pixPayload = $pixResponse->json()['payload'] ?? '';

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Erro ao gerar PIX Asaas: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Erro no Pagamento: ' . $e->getMessage());
        }

        $totalFinal = $order->total;
        return view('ecommerce.payment', compact('totalFinal', 'pixPayload', 'order'));
    }

    public function confirmPix(Request $request)
    {
        if (!Auth::check()) return redirect()->route('cart.index');

        $orderId = $request->input('order_id');
        $order = Order::where('user_id', Auth::id())->find($orderId);

        if (!$order) {
            return redirect()->route('cart.index')->with('error', 'Pedido de referência não localizado.');
        }

        $order->update(['status' => 'aguardando_confirmacao']);
        session()->forget(['cart', 'coupon', 'checkout_shipping_value', 'checkout_shipping_service_id']);

        return redirect()->route('shop.home')->with('success', 'Pedido gerado com sucesso!');
    }

    private function getOrCreateAsaasCustomer($user)
    {
        $cleanCpf = preg_replace('/\D/', '', $user->document ?? $user->cpf ?? '00000000000');

        if (!empty($cleanCpf) && $cleanCpf !== '00000000000') {
            $search = Http::withHeaders(['access_token' => $this->asaasKey])
                ->get($this->asaasUrl . '/customers', ['cpfCnpj' => $cleanCpf]);

            if ($search->successful() && !empty($search->json()['data'])) {
                return $search->json()['data'][0]['id'];
            }
        }

        $create = Http::withHeaders([
            'access_token' => $this->asaasKey,
            'Content-Type' => 'application/json'
        ])->post($this->asaasUrl . '/customers', [
            'name' => $user->name,
            'email' => $user->email,
            'cpfCnpj' => $cleanCpf,
            'phone' => preg_replace('/\D/', '', $user->phone ?? '11999999999'),
            'notificationDisabled' => true,
        ]);

        if ($create->failed()) {
            throw new \Exception('Erro ao cadastrar cliente no Asaas: ' . $create->body());
        }

        return $create->json()['id'];
    }
}
