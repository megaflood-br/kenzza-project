<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $carrinho = session('cart', []);

        if (empty($carrinho)) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        // Cálculo do Total baseado no que o CartController salvou (já com margem)
        $subtotal = 0;
        foreach ($carrinho as $item) {
            // Como o CartController agora salva o 'preco' correto, usamos ele diretamente
            $subtotal += (float)$item['preco'] * (int)$item['quantidade'];
        }

        // Pega o frete vindo do formulário
        $frete = (float) $request->input('shipping_value', 0);
        $totalFinal = $subtotal + $frete;

        $externalId = 'KENZZA-' . time() . '-' . Auth::id();

        try {
            // Cria o pedido no banco com o valor real de venda
            Order::create([
                'user_id' => Auth::id(),
                'total' => $totalFinal,
                'status' => 'pendente',
                'external_id' => $externalId
            ]);

            // Integração InfinitePay
            // handle: renoveemp (conforme seu env)
            // amount: valor em centavos (ex: 12108 para R$ 121,08)
            $response = Http::post(env('INFINITEPAY_URL'), [
                'handle' => env('INFINITEPAY_HANDLE'),
                'amount' => (int) round($totalFinal * 100),
                'order_id' => $externalId,
                'redirect_url' => route('shop.obrigado'),
            ]);

            if ($response->successful()) {
                $payment = $response->json();
                session()->forget('cart');
                return redirect($payment['url']);
            }

            Log::error('Erro na API InfinitePay Kenzza', ['res' => $response->json()]);
            return back()->with('error', 'Erro ao gerar link de pagamento.');

        } catch (\Exception $e) {
            Log::error('Erro Checkout Kenzza:', ['msg' => $e->getMessage()]);
            return back()->with('error', 'Falha no processamento do pedido.');
        }
    }

    public function success()
    {
        return view('ecommerce.thanks');
    }
}
