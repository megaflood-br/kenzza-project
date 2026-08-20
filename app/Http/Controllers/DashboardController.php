<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Visão Geral (Admin, Manager, Editor)
        if (in_array($user->role, ['admin', 'manager', 'editor'])) {

            $pedidosB2B = Order::whereIn('origin', ['distributor', 'representative'])
                ->whereIn('status', ['aprovado', 'em_separacao', 'enviado', 'entregue'])
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->get();

            $faturamentoB2B = 0;
            foreach ($pedidosB2B as $pedido) {
                $valorLimpo = is_string($pedido->total) ? (float) str_replace(',', '.', $pedido->total) : (float) $pedido->total;
                $faturamentoB2B += $valorLimpo;
            }

            $faturamentoEcommerce = 0;
            if ($user->role === 'admin') {
                $pedidosEcommerce = Order::whereNull('origin')
                    ->whereIn('status', ['aprovado', 'em_separacao', 'enviado', 'entregue'])
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->get();
                foreach ($pedidosEcommerce as $pedido) {
                    $valorLimpo = is_string($pedido->total) ? (float) str_replace(',', '.', $pedido->total) : (float) $pedido->total;
                    $faturamentoEcommerce += $valorLimpo;
                }
            }

            $pedidosMesCount = Order::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();
            $aguardandoLiberacao = Order::whereIn('origin', ['distributor', 'representative'])
                ->whereIn('status', ['pendente', 'aguardando_confirmacao', 'em_separacao'])->count();
            $totalDistribuidores = User::whereIn('role', ['distributor', 'representative'])->count();
            $ultimosPedidosB2B = Order::whereIn('origin', ['distributor', 'representative'])->with('user')->orderBy('created_at', 'desc')->take(5)->get();
            $comissoesMes = Order::whereNotNull('comissao_gerente')->whereIn('status', ['aprovado', 'em_separacao', 'enviado', 'entregue'])->sum('comissao_gerente');

            return view('dashboard', compact('faturamentoB2B', 'faturamentoEcommerce', 'pedidosMesCount', 'aguardandoLiberacao', 'totalDistribuidores', 'ultimosPedidosB2B', 'comissoesMes'));
        }

        // 2. Visão Restrita (Representante)
        // No DashboardController.php, altere o IF do representante para isto:

if ($user->role === 'representative') {
    // Redireciona o Representante direto para o painel de pedidos (ou uma view exclusiva dele)
    // Se você quer que ele caia no dashboard, vamos apenas limpar os dados:
    return view('dashboard', [
        'isRepresentative' => true, // Variável para a view esconder os cards
        'ultimosPedidosB2B' => Order::where('user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)->get()
    ]);
}

        return redirect()->route('painel.redirect');
    }
}
