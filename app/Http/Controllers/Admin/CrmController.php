<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    /**
     * Lista a carteira de Distribuidores
     */
    public function index()
{
    if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
        abort(403, 'Acesso não autorizado.');
    }

    // Altere de: User::where('role', 'distributor')
    // Para: User::whereIn('role', ['distributor', 'representative'])
    $distributors = User::whereIn('role', ['distributor', 'representative'])
        ->withCount('orders')
        ->withSum('orders', 'total')
        ->orderBy('name', 'asc')
        ->paginate(20);

    return view('admin.crm.index', compact('distributors'));
}

    /**
     * Tela de emissão manual de pedidos (Televendas)
     */
    public function createOrder()
    {
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403, 'Acesso não autorizado.');
        }

        // Carrega tanto os distribuidores quanto os novos representantes para o Select do formulário
        $distributors = User::whereIn('role', ['distributor', 'representative'])
            ->orderBy('name', 'asc')
            ->get();

        // CORREÇÃO: Puxa os produtos usando a coluna real 'nome' e sem a trava de 'status' que não existe
        $products = Product::orderBy('nome', 'asc')->get();

        return view('admin.crm.create_order', compact('distributors', 'products'));
    }

    /**
     * Salva o pedido manual no banco de dados
     */
    public function storeOrder(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'distributor_id' => 'required|exists:users,id',
            'items' => 'required|array',
            'frete' => 'nullable|numeric|min:0',
            'pagamento' => 'required|string|max:255'
        ]);

        // Busca o cliente (pode ser distribuidor ou representante)
        $customer = User::findOrFail($request->distributor_id);

        // Define a string de origem baseada no nível do cliente escolhido
        $origin = $customer->role === 'representative' ? 'representative' : 'distributor';

        $totalProdutos = 0;
        $totalComissaoGerente = 0;
        $orderItems = [];

        // Verifica os itens e calcula os subtotais aplicando a regra de negócio
        foreach ($request->items as $productIdId => $quantidade) {
            if ($quantidade > 0) {
                $product = Product::find($productIdId);

                if ($product) {
                    $valorOriginal = $product->preco_distribuidor;
                    $precoBase = is_string($valorOriginal) && str_contains($valorOriginal, ',')
                        ? (float) str_replace(['.', ','], ['', '.'], $valorOriginal)
                        : (float) $valorOriginal;

                    // Se for Representante, aplica acréscimo de 30% no preço unitário do item
                    if ($customer->role === 'representative') {
                        $precoFinalItem = $precoBase * 1.30;
                        $comissaoUnidade = $precoFinalItem - $precoBase;
                        $totalComissaoGerente += ($comissaoUnidade * $quantidade);
                    } else {
                        $precoFinalItem = $precoBase;
                    }

                    $subtotal = $precoFinalItem * $quantidade;
                    $totalProdutos += $subtotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'quantidade' => $quantidade,
                        'preco_unitario' => $precoFinalItem,
                        'subtotal' => $subtotal,
                    ];
                }
            }
        }

        if (empty($orderItems)) {
            return back()->with('error', 'O pedido precisa ter pelo menos um produto selecionado.');
        }

        $frete = $request->frete ? (float) $request->frete : 0;
        $totalFinal = $totalProdutos + $frete;

        // Cria o Pedido principal
        $order = \App\Models\Order::create([
            'user_id' => $customer->id,
            'status' => 'aprovado',
            'origin' => $origin,
            'total' => $totalFinal,
            'frete' => $frete,
            'metodo_pagamento' => $request->pagamento,
            // Salva de forma fixa a comissão obtida caso o cliente seja representante
            'comissao_gerente' => $customer->role === 'representative' ? $totalComissaoGerente : null,
        ]);

        // Grava os itens na tabela filha (order_items)
        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Pedido comercial emitido com sucesso!');
    }
}
