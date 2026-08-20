<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Category;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PriceTableExport;

class PriceTableController extends Controller
{
    private function parsePriceCsv()
    {
        // Tenta os caminhos mais prováveis na Hostinger/TinyCP
        $paths = [
            storage_path('app/public/tabela_precos.csv'),
            public_path('storage/tabela_precos.csv'),
            base_path('storage/app/public/tabela_precos.csv'),
        ];

        $path = null;
        foreach ($paths as $p) {
            if (file_exists($p)) {
                $path = $p;
                break;
            }
        }

        // Se não encontrar o arquivo, em vez de abort(500), vamos lançar uma exceção
        if (!$path) {
            throw new Exception("Arquivo CSV não encontrado. Caminhos testados: " . implode(' | ', $paths));
        }

        $categories = [];
        $lines = file($path);
        $currentFamily = 'GERAL';

        foreach ($lines as $line) {
            // Converte encoding e limpa caracteres invisíveis
            $line = mb_convert_encoding($line, 'UTF-8', 'ISO-8859-1');
            $line = preg_replace('/[\x00-\x1F\x7F-\x9F\xEF\xBB\xBF]/u', '', $line);

            $columns = explode(';', $line);
            $columns = array_map('trim', $columns);

            $tamanho = '';
            $precoRaw = '';
            $nomeProduto = '';

            foreach ($columns as $val) {
                if ($val === '' || $val === '.') continue;

                if (str_contains($val, 'R$')) {
                    $precoRaw = $val;
                }
                elseif (preg_match('/(\d+[,.]?\d*\s?(ml|gr|l|kg|grs|g|1,5\s?l))/i', $val)) {
                    $tamanho = $val;
                }
                elseif (empty($nomeProduto) && strlen($val) > 1) {
                    $nomeProduto = $val;
                }
            }

            if (!empty($nomeProduto) && empty($tamanho) && empty($precoRaw)) {
                $currentFamily = strtoupper($nomeProduto);
                continue;
            }

            if (!empty($nomeProduto) && !empty($precoRaw)) {
                $cleanPrice = str_replace(['R$', ' '], '', $precoRaw);

                if (str_contains($cleanPrice, ',')) {
                    $cleanPrice = str_replace('.', '', $cleanPrice);
                    $cleanPrice = str_replace(',', '.', $cleanPrice);
                }

                $valorFinal = (float) $cleanPrice;

                $categories[$currentFamily][] = [
                    'produto' => $nomeProduto,
                    'tamanho' => $tamanho,
                    'preco'   => $valorFinal,
                ];
            }
        }

        return $categories;
    }

    public function publicTable()
    {
        try {
            $categories = $this->parsePriceCsv();
            $titulo = "Distribuidor";
            return view('public.prices', compact('categories', 'titulo'));
        } catch (Exception $e) {
            return response("Erro ao processar tabela: " . $e->getMessage(), 500);
        }
    }
    // NOVO MÉTODO: Exclusivo e cravado com +30%
    public function representativeTable()
    {
        try {
            $rawData = $this->parsePriceCsv();
            $categories = [];
            $titulo = "Representante Comercial";

            foreach ($rawData as $familia => $produtos) {
                foreach ($produtos as $produto) {
                    // Aplica 30% fixo no backend, independente de quem acessa o link
                    $produto['preco'] = $produto['preco'] * 1.30;
                    $categories[$familia][] = $produto;
                }
            }
            return view('public.prices', compact('categories', 'titulo'));
        } catch (Exception $e) {
            return response("Erro ao processar tabela: " . $e->getMessage(), 500);
        }
    }

    public function salonTable()
    {
        try {
            $rawData = $this->parsePriceCsv();
            $categories = [];
            $titulo = "Salão de Beleza";

            foreach ($rawData as $familia => $produtos) {
                foreach ($produtos as $produto) {
                    $isColoracao = str_contains(strtoupper($produto['produto']), 'COR') ||
                                   str_contains(strtoupper($familia), 'COLOR');

                    $multiplicador = $isColoracao ? 2.0 : 2.2;
                    $produto['preco'] = $produto['preco'] * $multiplicador;

                    $categories[$familia][] = $produto;
                }
            }
            return view('public.prices', compact('categories', 'titulo'));
        } catch (Exception $e) {
            return response("Erro ao processar tabela: " . $e->getMessage(), 500);
        }
    }

    public function index(Request $request)
    {
        $selectedCategory = $request->query('categoria');
        $allCategories = Category::has('products')->get();
        $categories = Category::with(['products' => fn($q) => $q->where('estoque', '>', 0)])
            ->when($selectedCategory, fn($query) => $query->where('id', $selectedCategory))
            ->get();

        $productsWithoutCategory = collect();
        if (!$selectedCategory) {
            $productsWithoutCategory = Product::whereNull('category_id')->where('estoque', '>', 0)->get();
        }

        return view('distributor.prices.index', compact('categories', 'productsWithoutCategory', 'allCategories', 'selectedCategory'));
    }

    public function exportPdf(Request $request)
    {
        $selectedCategory = $request->query('categoria');
        $user = auth()->user();
        $categories = Category::with(['products' => fn($q) => $q->where('estoque', '>', 0)])->get();
        $productsWithoutCategory = collect();

        $logoPath = public_path('logo-k-preto.png');
        $logoData = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : "";

        $pdf = Pdf::loadView('pdf.price-table', compact('categories', 'productsWithoutCategory', 'user', 'logoData'));
        return $pdf->download('tabela-precos-kenzza.pdf');
    }

    public function exportXls()
    {
        return Excel::download(new PriceTableExport, 'tabela-precos-kenzza.xlsx');
    }
}
