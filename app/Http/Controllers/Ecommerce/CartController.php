<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartModel = $this->getOrCreateCart();
        $cart = $this->formatCartForView($cartModel);

        // Mantém a sessão atualizada para compatibilidade com outras partes do sistema
        session()->put('cart', $cart);

        $cep = null;
        $user = null;

        if (auth()->check()) {
            $user = auth()->user();
            $rawCep = $user->cep ?? $user->zip_code ?? $user->postcode;
            $cep = preg_replace('/[^0-9]/', '', $rawCep);

            if ($cep && !session()->has('shipping_cep')) {
                session()->put('shipping_cep', $cep);
            }
        }

        // Toda vez que abrir a página do carrinho, revalida o cupom por segurança
        $this->revalidateCoupon($cartModel);

        return view('ecommerce.cart', compact('cart', 'cep', 'user'));
    }

    public function add(Product $product)
    {
        $cartModel = $this->getOrCreateCart();

        // Procura se o produto já existe neste carrinho dentro do banco de dados
        $cartItem = CartItem::where('cart_id', $cartModel->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantidade');
        } else {
            CartItem::create([
                'cart_id' => $cartModel->id,
                'product_id' => $product->id,
                'quantidade' => 1
            ]);
        }

        // Força a atualização do timestamp 'updated_at' do carrinho para sabermos quando foi modificado
        $cartModel->touch();

        // REVALIDAÇÃO: Recalcula o cupom após adicionar um item
        $this->revalidateCoupon($cartModel);

        return redirect()->route('cart.index')->with('success', 'Produto adicionado ao carrinho!');
    }

    public function update(Request $request, $id)
    {
        $cartModel = $this->getOrCreateCart();
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['sucesso' => false, 'erro' => 'Produto não encontrado.'], 404);
        }

        if ($request->quantidade > $product->estoque) {
            return response()->json([
                'sucesso' => false,
                'erro' => 'Quantidade indisponível em estoque. Máximo: ' . $product->estoque
            ], 400);
        }

        if ($request->quantidade > 0) {
            $cartItem = CartItem::where('cart_id', $cartModel->id)
                ->where('product_id', $id)
                ->first();

            if ($cartItem) {
                $cartItem->update(['quantidade' => (int)$request->quantidade]);
                $cartModel->touch();
            }

            // REVALIDAÇÃO: Recalcula o cupom após alterar a quantidade
            $this->revalidateCoupon($cartModel);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['sucesso' => true, 'mensagem' => 'Carrinho atualizado!']);
            }

            return redirect()->back()->with('success', 'Carrinho atualizado!');
        }

        if ($request->wantsJson()) {
            return response()->json(['sucesso' => false, 'erro' => 'Quantidade inválida.'], 400);
        }

        return redirect()->back()->with('error', 'Quantidade inválida.');
    }

    public function remove($id)
    {
        $cartModel = $this->getOrCreateCart();

        CartItem::where('cart_id', $cartModel->id)
            ->where('product_id', $id)
            ->delete();

        $cartModel->touch();

        // REVALIDAÇÃO: Recalcula o cupom após remover um item
        $this->revalidateCoupon($cartModel);

        return redirect()->back()->with('success', 'Produto removido!');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['codigo' => 'required|string']);

        $cupom = Coupon::with('categories')->where('codigo', strtoupper($request->codigo))
            ->where('ativo', true)
            ->where(function($query) {
                $query->whereNull('validade')->orWhere('validade', '>=', now());
            })
            ->first();

        if (!$cupom) {
            return response()->json(['sucesso' => false, 'erro' => 'Cupom inválido ou expirado.']);
        }

        if ($cupom->limite_uso > 0 && $cupom->vezes_usado >= $cupom->limite_uso) {
            return response()->json(['sucesso' => false, 'erro' => 'Limite de uso atingido.']);
        }

        $cartModel = $this->getOrCreateCart();
        $cart = $this->formatCartForView($cartModel);

        if (empty($cart)) {
            return response()->json(['sucesso' => false, 'erro' => 'O carrinho está vazio.']);
        }

        // Grava o código do cupom diretamente na tabela do carrinho no banco
        $cartModel->update(['coupon_code' => $cupom->codigo]);
        session()->put('coupon_code', $cupom->codigo);

        // Executa o cálculo dinâmico centralizado
        $resultado = $this->calculateCouponDiscount($cupom, $cart);

        if (!$resultado['sucesso']) {
            $cartModel->update(['coupon_code' => null]);
            session()->forget('coupon_code');
            return response()->json(['sucesso' => false, 'erro' => $resultado['erro']]);
        }

        return response()->json(['sucesso' => true, 'mensagem' => 'Cupom aplicado com sucesso!']);
    }

    /**
     * LOCALIZA OU CRIA O CARRINHO NO BANCO DE DADOS
     */
    private function getOrCreateCart()
    {
        if (auth()->check()) {
            // Se o usuário estiver logado, busca pelo ID dele
            $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

            // Segurança: Se ele tinha um carrinho de visitante antes de logar, transfere os itens
            $sessionId = session()->getId();
            $guestCart = Cart::where('session_id', $sessionId)->first();
            if ($guestCart) {
                foreach ($guestCart->items as $item) {
                    CartItem::firstOrCreate(
                        ['cart_id' => $cart->id, 'product_id' => $item->product_id],
                        ['quantidade' => $item->quantidade]
                    );
                }
                $guestCart->delete(); // Apaga o carrinho temporário de visitante
            }
            return $cart;
        }

        // Se for visitante, busca pela Session ID do navegador
        return Cart::firstOrCreate(['session_id' => session()->getId()]);
    }

    /**
     * CONVERTE OS DADOS DO BANCO PARA O FORMATO DE ARRAY QUE A VIEW JÁ ESPERA
     */
    private function formatCartForView($cartModel)
    {
        if (!$cartModel) return [];

        $formatted = [];
        // Carrega os itens com os produtos associados
        foreach ($cartModel->items()->with('product')->get() as $item) {
            $product = $item->product;
            if ($product) {
                $formatted[$product->id] = [
                    "product_id"  => $product->id,
                    "nome"        => $product->nome,
                    "quantidade"  => $item->quantidade,
                    "preco"       => $product->preco_atual,
                    "imagem"      => $product->imagem,
                    "peso"        => $product->peso,
                    "largura"     => $product->largura,
                    "altura"      => $product->altura,
                    "comprimento" => $product->comprimento,
                    "category_id" => $product->category_id
                ];
            }
        }
        return $formatted;
    }

    /**
     * REVALIDAÇÃO DE CUPOM BASEADA NO MODELO DO BANCO
     */
    private function revalidateCoupon($cartModel)
    {
        $couponCode = $cartModel->coupon_code ?? session()->get('coupon_code');
        $cart = $this->formatCartForView($cartModel);

        if (empty($cart) || !$couponCode) {
            $cartModel->update(['coupon_code' => null]);
            session()->forget(['coupon', 'coupon_code']);
            return;
        }

        $cupom = Coupon::with('categories')->where('codigo', $couponCode)->where('ativo', true)->first();

        if (!$cupom) {
            $cartModel->update(['coupon_code' => null]);
            session()->forget(['coupon', 'coupon_code']);
            return;
        }

        $resultado = $this->calculateCouponDiscount($cupom, $cart);

        if (!$resultado['sucesso']) {
            $cartModel->update(['coupon_code' => null]);
            session()->forget(['coupon', 'coupon_code']);
        }
    }

    /**
     * COORDENADOR DE CÁLCULO DE DESCONTO
     */
    private function calculateCouponDiscount($cupom, $cart)
    {
        $subtotalGeral = array_sum(array_map(fn($item) => (float)$item['preco'] * (int)$item['quantidade'], $cart));
        $descontoCalculado = 0;

        if ($cupom->categories()->exists()) {
            $allowedCategoryIds = $cupom->categories->pluck('id')->toArray();
            $subtotalValido = 0;
            $hasValidProduct = false;

            foreach ($cart as $item) {
                $categoryId = $item['category_id'] ?? null;
                if (!$categoryId && isset($item['product_id'])) {
                    $prod = Product::find($item['product_id']);
                    $categoryId = $prod?->category_id;
                }

                if (in_array($categoryId, $allowedCategoryIds)) {
                    $hasValidProduct = true;
                    $subtotalValido += (float)$item['preco'] * (int)$item['quantidade'];
                }
            }

            if (!$hasValidProduct) {
                return ['sucesso' => false, 'erro' => 'Este cupom não se aplica a nenhuma categoria dos produtos inclusos.'];
            }

            if ($cupom->tipo === 'percentual') {
                $descontoCalculado = $subtotalValido * ((float)$cupom->valor / 100);
            } else {
                $descontoCalculado = min((float)$cupom->valor, $subtotalValido);
            }
        } else {
            if ($cupom->tipo === 'percentual') {
                $descontoCalculado = $subtotalGeral * ((float)$cupom->valor / 100);
            } else {
                $descontoCalculado = min((float)$cupom->valor, $subtotalGeral);
            }
        }

        session()->put('coupon', [
            'codigo' => $cupom->codigo,
            'tipo' => $cupom->tipo,
            'valor' => $cupom->valor,
            'discount' => $descontoCalculado
        ]);

        return ['sucesso' => true];
    }
}
