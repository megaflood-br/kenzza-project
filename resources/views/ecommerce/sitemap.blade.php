<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- 1. Links Estáticos --}}
    @foreach($staticUrls as $route)
        <url>
            <loc>{{ $route['url'] }}</loc>
            <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
            <changefreq>{{ $route['freq'] }}</changefreq>
            <priority>{{ $route['pri'] }}</priority>
        </url>
    @endforeach

    {{-- 2. Categorias Dinâmicas --}}
    @foreach($categories as $category)
        <url>
            <loc>{{ route('shop.index', $category->slug) }}</loc>
            <lastmod>{{ $category->updated_at ? $category->updated_at->format('Y-m-d') : now()->format('Y-m-d') }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    {{-- 3. Produtos Dinâmicos --}}
    @foreach($products as $product)
        <url>
            <loc>{{ route('shop.product', $product->slug) }}</loc>
            <lastmod>{{ $product->updated_at ? $product->updated_at->format('Y-m-d') : now()->format('Y-m-d') }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

</urlset>
