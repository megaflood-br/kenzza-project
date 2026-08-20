<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Gera o arquivo XML do Sitemap dinamicamente
     */
    public function index(): Response
    {
        // 1. Páginas estáticas institucionais da K'enzza com suas respectivas prioridades
        $staticUrls = [
            ['url' => route('shop.home'), 'pri' => '1.0', 'freq' => 'daily'],
            ['url' => route('shop.index'), 'pri' => '0.9', 'freq' => 'daily'],
            ['url' => route('shop.about'), 'pri' => '0.7', 'freq' => 'monthly'],
            ['url' => route('shop.membership'), 'pri' => '0.7', 'freq' => 'monthly'],
            ['url' => route('prices.salon'), 'pri' => '0.6', 'freq' => 'weekly'],
            ['url' => route('prices.public'), 'pri' => '0.6', 'freq' => 'weekly'],
            ['url' => route('shop.shipping'), 'pri' => '0.5', 'freq' => 'monthly'],
            ['url' => route('shop.terms'), 'pri' => '0.5', 'freq' => 'monthly'],
            ['url' => route('shop.lgpd'), 'pri' => '0.5', 'freq' => 'monthly'],
        ];

        // 2. Puxa todos os produtos ativos do banco de dados
        // Nota: Ajuste a coluna de status ou exclusão lógica caso utilize (ex: ->where('active', true))
        $products = Product::latest()->get();

        // 3. Puxa todas as categorias de produtos cadastradas
        $categories = Category::all();

        // Retorna a view com o cabeçalho correto de XML para os robôs do Google (Content-Type)
        return response()->view('ecommerce.sitemap', [
            'staticUrls' => $staticUrls,
            'products'   => $products,
            'categories' => $categories,
        ])->header('Content-Type', 'text/xml');
    }
}
