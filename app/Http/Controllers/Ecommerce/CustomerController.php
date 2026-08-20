<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Resumo do Painel do Cliente
     */
    public function index()
    {

        $user = Auth::user();

        // 2. Agora sim podemos verificar a propriedade dele
        if (!$user->email_verified_at) {
            return redirect()->route('verification.notice');
        }
        // Pega os últimos 3 pedidos para o resumo
        $recentOrders = Order::where('user_id', $user->id)->latest()->take(3)->get();

        return view('ecommerce.customer.panel', compact('user', 'recentOrders'));
    }

    /**
     * Listagem completa de pedidos
     */
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);
        return view('ecommerce.customer.orders', compact('orders'));
    }

    /**
     * Detalhes de um pedido específico
     */
    public function showOrder(Order $order)
    {
        // 1. Trava de segurança: o pedido precisa pertencer ao usuário logado
        abort_if($order->user_id !== Auth::id(), 403);

        // 2. CORREÇÃO: Carrega os itens do pedido e os dados do produto de cada item
        // Isso resolve o problema de a lista aparecer vazia na View
        $order->load(['items.product']);

        return view('ecommerce.customer.order-show', compact('order'));
    }
}
