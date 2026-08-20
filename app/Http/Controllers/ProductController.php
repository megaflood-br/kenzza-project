<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Services\MelhorEnvioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Função auxiliar para converter moeda brasileira
     */
    private function parseCurrency($value)
    {
        if (is_null($value) || $value === '') return 0;
        if (is_numeric($value)) return (float) $value;

        if (str_contains($value, ',')) {
            $clean = str_replace('.', '', $value);
            $clean = str_replace(',', '.', $clean);
            return (float) $clean;
        }

        return (float) $value;
    }

    /**
     * HOME: Exibe banners e produtos em destaque
     */
    public function home()
    {
        $banners = Banner::where('ativo', true)->orderBy('ordem')->get();

        $categories = Category::whereHas('products', function ($query) {
            $query->whereNull('deleted_at');
        })->get();

        $featuredProducts = Product::whereNull('deleted_at')
            ->where('estoque', '>', 0)
            ->inRandomOrder()
            ->take(8)
            ->get()
            ->map(function($product) {
                $p = $product->fresh();
                $p->preco_limpo = $p->preco_atual;
                return $p;
            });

        return view('ecommerce.home', compact('banners', 'featuredProducts', 'categories'));
    }

    /**
     * LOJA (LISTAGEM) - Atualizado com suporte a Rolagem Infinita via AJAX
     */
    public function shopIndex(Request $request, $slug = null)
    {
        $categories = Category::all();
        $category = null;

        // Inicia a query base trazendo os produtos ativos
        $query = Product::whereNull('deleted_at')->where('estoque', '>', 0);

        // Filtro por Categoria via Slug na URL
        if ($slug) {
            $category = Category::where('slug', $slug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filtro por Busca Textual
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->search . '%')
                  ->orWhere('descricao', 'like', '%' . $request->search . '%');
            });
        }

        // CORREÇÃO: Filtros ajustados para bater com o preco_atual dinâmico
        if ($request->filled('min_price')) {
            $query->where('preco_distribuidor', '>=', $this->parseCurrency($request->min_price));
        }

        if ($request->filled('max_price')) {
            $query->where('preco_distribuidor', '<=', $this->parseCurrency($request->max_price));
        }

        // Ordenação Dinâmica adaptada
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('preco_distribuidor', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('preco_distribuidor', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('nome', 'asc');
                break;
        }

        // Paginação por blocos de carregamento da rolagem infinita
        $products = $query->paginate(6)->withQueryString();

        // Injeta os hooks de preços limpos dinâmicos nas instâncias carregadas
        $products->getCollection()->transform(function($product) {
            $p = $product->fresh();
            $p->preco_limpo = $p->preco_atual;
            return $p;
        });

        // REQUISITO DO SCROLL INFINITO: Se for requisição AJAX, envia apenas o fragmento HTML dos cards
        if ($request->ajax()) {
            return view('ecommerce.partials.product-cards', compact('products'))->render();
        }

        // Renderiza a view da vitrine completa (mapeada no seu diretório correto)
        return view('ecommerce.index', compact('products', 'categories', 'category', 'slug'));
    }

    /**
     * DETALHES DO PRODUTO (Público)
     */
    public function showShop($slug_ou_id)
    {
        // Busca o produto principal (suporta slug ou id)
        $product = Product::with('category')
            ->where('slug', $slug_ou_id)
            ->orWhere('id', $slug_ou_id)
            ->firstOrFail()
            ->fresh();

        $product->preco_limpo = $product->preco_atual;

        // --- LÓGICA DE PRODUTOS RELACIONADOS ---
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereNull('deleted_at')
            ->where('estoque', '>', 0)
            ->take(4)
            ->get();

        $cep = null;
        if (auth()->check()) {
            $user = auth()->user();
            $rawCep = $user->cep ?? $user->zip_code;
            $cep = preg_replace('/[^0-9]/', '', $rawCep);
        }
        return view('ecommerce.show', compact('product', 'cep', 'relatedProducts'));
    }

    /**
     * CÁLCULO DE FRETE (MELHOR ENVIO)
     */
    public function calcularFrete(Request $request, MelhorEnvioService $freteService)
    {
        $request->validate(['cep' => 'required|string|size:8']);

        try {
            $produtosParaCalculo = [];

            if ($request->filled('product_id')) {
                $product = Product::findOrFail($request->product_id);
                $produtosParaCalculo[] = [
                    'id' => $product->sku,
                    'width' => (float)$product->largura,
                    'height' => (float)$product->altura,
                    'length' => (float)$product->comprimento,
                    'weight' => (float)$product->peso,
                    'insurance_value' => (float)$product->preco_atual,
                    'quantity' => 1
                ];
            } else {
                $cart = session()->get('cart', []);
                if (empty($cart)) return response()->json(['sucesso' => false, 'erro' => 'Carrinho vazio.']);

                foreach ($cart as $item) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $produtosParaCalculo[] = [
                            'id' => $product->sku,
                            'width' => (float)$product->largura,
                            'height' => (float)$product->altura,
                            'length' => (float)$product->comprimento,
                            'weight' => (float)$product->peso,
                            'insurance_value' => (float)$product->preco_atual,
                            'quantity' => $item['quantidade']
                        ];
                    }
                }
            }

            $resultado = $freteService->calcularFrete($request->cep, $produtosParaCalculo);

            if (!$resultado) return response()->json(['sucesso' => false, 'erro' => 'API offline.']);

            $opcoes = collect($resultado)->filter(function($o) {
                $name = strtolower($o['name'] ?? '');
                return !str_contains($name, 'azul') && !str_contains($name, 'latam') && isset($o['price']);
            })->map(function($o) {
                return [
                    'id' => $o['id'], // <- CORREÇÃO: O ID DA TRANSPORTADORA AGORA É ENVIADO!
                    'nome' => $o['company']['name'] . ' (' . $o['name'] . ')',
                    'valor' => (float) $o['price'],
                    'prazo' => $o['delivery_range']['max'] . ' dias'
                ];
            })->values();

            return response()->json(['sucesso' => true, 'opcoes' => $opcoes]);

        } catch (\Exception $e) {
            Log::error("Erro Frete: " . $e->getMessage());
            return response()->json(['sucesso' => false, 'erro' => 'Erro interno.']);
        }
    }

    /* --- MODO ADMIN --- */

    public function index()
    {
        $products = Product::with('category')->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->merge(['preco_distribuidor' => $this->parseCurrency($request->preco_distribuidor)]);

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'ean'  => 'nullable|string|max:15',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|unique:products,sku',
            'descricao' => 'nullable|string',
            'preco_distribuidor' => 'required|numeric',
            'estoque' => 'required|integer',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:12048',
            'peso' => 'nullable|numeric',
            'largura' => 'nullable|numeric',
            'altura' => 'nullable|numeric',
            'comprimento' => 'nullable|numeric',
        ]);

        $data['slug'] = Str::slug($request->nome);

        if ($request->hasFile('imagem')) {
            $data['imagem'] = $request->file('imagem')->store('products', 'public');
        }

        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Produto K\'enzza cadastrado!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->merge(['preco_distribuidor' => $this->parseCurrency($request->preco_distribuidor)]);

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'ean'  => 'nullable|string|max:15',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'descricao' => 'nullable|string',
            'preco_distribuidor' => 'required|numeric',
            'estoque' => 'required|integer',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:12048',
            'peso' => 'nullable|numeric',
            'largura' => 'nullable|numeric',
            'altura' => 'nullable|numeric',
            'comprimento' => 'nullable|numeric',
        ]);

        $data['slug'] = Str::slug($request->nome);

        if ($request->hasFile('imagem')) {
            if ($product->imagem) Storage::disk('public')->delete($product->imagem);
            $data['imagem'] = $request->file('imagem')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Produto updated!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->imagem) Storage::disk('public')->delete($product->imagem);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produto removido!');
    }
}
