<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; color: #333; }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f4f4f4;
            padding-bottom: 20px;
        }

        /* Ajuste do Logo */
        .logo-img {
            max-height: 60px;
            margin-bottom: 10px;
        }

        .tier-badge {
            color: #c5a059;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            display: block;
        }

        .category-title {
            background-color: #f9f9f9;
            padding: 8px 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #c5a059;
            border-left: 4px solid #c5a059;
            margin-top: 20px;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; font-size: 10px; color: #999; padding: 10px; border-bottom: 1px solid #eee; }
        td { padding: 10px; border-bottom: 1px solid #f4f4f4; font-size: 11px; }
        .price { text-align: right; font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>

    <div class="header">
        @if($logoData)
            <img src="{{ $logoData }}" class="logo-img">
        @else
            <div style="font-size: 24px; font-weight: bold;">K'enzza</div>
        @endif

        <div class="tier-badge">Tabela de Preços: {{ $user->tier }}</div>
        <div style="font-size: 10px; color: #999;">Emissão: {{ date('d/m/Y H:i') }}</div>
    </div>

    @foreach($categories as $category)
        @if($category->products->count() > 0)
            <div class="category-title">{{ $category->nome }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="75%">Produto</th>
                        <th width="25%" style="text-align: right;">Preço Unitário</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category->products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->nome }}</strong><br>
                                <span style="color: #999; font-size: 9px;">SKU: {{ $product->sku ?? '---' }}</span>
                            </td>
                            <td class="price">R$ {{ number_format($product->preco_atual, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    {{-- Exibir sem categoria se houver --}}
    @if($productsWithoutCategory->count() > 0)
        <div class="category-title">Outros</div>
        <table>
            <tbody>
                @foreach($productsWithoutCategory as $product)
                    <tr>
                        <td width="75%">
                            <strong>{{ $product->nome }}</strong><br>
                            <span style="color: #999; font-size: 9px;">SKU: {{ $product->sku ?? '---' }}</span>
                        </td>
                        <td width="25%" class="price">R$ {{ number_format($product->preco_atual, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>
