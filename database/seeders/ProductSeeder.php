<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'nome' => 'Kit Reconstrução Profissional 1L',
                'sku' => 'KEN-RECON-01',
                'descricao' => 'Tratamento intensivo para fios danificados por processos químicos.',
                'preco_black' => 250.00,
                'preco_gold' => 220.00,
                'preco_diamante' => 190.00,
                'estoque' => 50,
                'imagem' => 'kit-reconstrucao.jpg',
            ],
            [
                'nome' => 'Sérum Finalizador Dourado 60ml',
                'sku' => 'KEN-SERUM-60',
                'descricao' => 'Brilho intenso e proteção térmica com partículas de ouro.',
                'preco_black' => 85.00,
                'preco_gold' => 75.00,
                'preco_diamante' => 60.00,
                'estoque' => 100,
                'imagem' => 'serum-dourado.jpg',
            ],
            [
                'nome' => 'Máscara Hidratação Profunda 500g',
                'sku' => 'KEN-MASK-500',
                'descricao' => 'Hidratação de alto impacto para uso em lavatório.',
                'preco_black' => 120.00,
                'preco_gold' => 105.00,
                'preco_diamante' => 88.00,
                'estoque' => 30,
                'imagem' => 'mascara-hidra.jpg',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
