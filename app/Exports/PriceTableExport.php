<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PriceTableExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection() {
        return Product::where('estoque', '>', 0)->get();
    }

    public function headings(): array {
        return ['Produto', 'SKU', 'Seu Preço (R$)', 'Estoque'];
    }

    public function map($product): array {
        return [
            $product->nome,
            $product->sku,
            number_format($product->preco_atual, 2, ',', '.'),
            $product->estoque
        ];
    }
}
