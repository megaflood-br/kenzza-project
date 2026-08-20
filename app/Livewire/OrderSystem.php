<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderSystem extends Component
{
    public $cart = [];
    public $total = 0;
    public $paymentMethod = '';
    public $selectedCategory = null;

    // Método para adicionar ao carrinho
    public function addToCart($productId, $quantity = 1)
    {
        if ($quantity < 1) return;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId] += $quantity;
        } else {
            $this->cart[$productId] = $quantity;
        }

        $this->calculateTotal();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $id => $qty) {
            $product = Product::find($id);
            if ($product) {
                $this->total += $product->preco_atual * $qty;
            }
        }
    }

    public function render()
    {
        $query = Product::where('estoque', '>', 0);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        return view('livewire.order-system', [
            'products' => $query->get(),
            'categories' => \App\Models\Category::all(),
            'cartItems' => collect($this->cart)->map(function($qty, $id) {
                $product = Product::find($id);
                if (!$product) return null;

                return [
                    'id' => $id,
                    'nome' => $product->nome,
                    'preco' => $product->preco_atual,
                    'qty' => $qty,
                    'subtotal' => $product->preco_atual * $qty
                ];
            })->filter()
        ]);
    }

    public function checkout()
    {
        if (empty($this->cart)) return;

        // Validação atualizada para aceitar o 'cheque'
        if (empty($this->paymentMethod) || !in_array($this->paymentMethod, ['pix', 'boleto', 'cartao', 'cheque'])) {
            session()->flash('error', 'Por favor, selecione uma forma de pagamento válida (PIX, Boleto, Cartão ou Cheque).');
            return;
        }

        DB::transaction(function () {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $this->total,
                'metodo_pagamento' => $this->paymentMethod,
                'status' => 'pendente',
            ]);

            foreach ($this->cart as $id => $qty) {
                $product = Product::find($id);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantidade' => $qty,
                    'preco_unitario' => $product->preco_atual,
                    'subtotal' => $product->preco_atual * $qty
                ]);

                $product->decrement('estoque', $qty);
            }
        });

        $this->cart = [];
        $this->total = 0;
        $this->paymentMethod = '';

        session()->flash('success', 'Pedido realizado com sucesso!');
        return redirect()->route('orders.index')->with('success', 'Pedido realizado com sucesso!');
    }
}
