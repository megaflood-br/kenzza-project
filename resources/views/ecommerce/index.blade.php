@section('title', 'Home')
@section('seo')
    <meta name="description" content="Explore a linha de {{ $category->nome ?? 'produtos' }} da K'enzza Hair. Qualidade profissional para transformação e cuidado capilar.">
@endsection

<x-store-layout>
    <div class="bg-gray-50 min-h-screen py-12"
         x-data="{
            page: 1,
            nextPageUrl: '{{ $products->nextPageUrl() }}',
            loading: false,
            hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},

            init() {
                const observer = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting && !this.loading && this.hasMore) {
                        this.loadMoreProducts();
                    }
                }, { rootMargin: '200px' });

                observer.observe(this.$refs.infiniteMarker);
            },

            async loadMoreProducts() {
                if (!this.nextPageUrl) return;

                this.loading = true;
                try {
                    let currentUrl = new URL(this.nextPageUrl);
                    let urlParams = new URLSearchParams(window.location.search);
                    urlParams.forEach((value, key) => {
                        if(key !== 'page') currentUrl.searchParams.set(key, value);
                    });

                    const response = await fetch(currentUrl.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (response.ok) {
                        const html = await response.text();

                        document.getElementById('product-grid-container').insertAdjacentHTML('beforeend', html);

                        this.page++;

                        let nextPageNum = this.page + 1;

                        if (this.page >= {{ $products->lastPage() }}) {
                            this.hasMore = false;
                            this.nextPageUrl = null;
                        } else {
                            currentUrl.searchParams.set('page', nextPageNum);
                            this.nextPageUrl = currentUrl.toString();
                        }
                    }
                } catch (e) {
                    console.error('Erro ao processar rolagem infinita', e);
                } finally {
                    this.loading = false;
                }
            }
         }"
         x-init="init()">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
            <div class="flex flex-col lg:flex-row gap-12">

                {{-- Barra Lateral: Filtros --}}
                <aside class="w-full lg:w-64 space-y-10">
                    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                        <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-[0.2em] mb-6">Filtrar Preço</h3>
                        <form action="{{ route('shop.index', request('slug')) }}" method="GET" class="space-y-4">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Mín" class="w-full bg-gray-50 border-none rounded-xl text-xs focus:ring-[#B8860B]">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Máx" class="w-full bg-gray-50 border-none rounded-xl text-xs focus:ring-[#B8860B]">
                            <button type="submit" class="w-full bg-black text-white text-[10px] font-black py-3 rounded-xl hover:bg-[#B8860B] transition-all uppercase tracking-widest">Aplicar</button>
                        </form>
                    </div>

                    <div class="px-4 hidden lg:block">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Nossas Linhas</h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('shop.index') }}"
                                   class="text-sm {{ !request('slug') ? 'text-[#B8860B] font-bold' : 'text-gray-600 hover:text-black' }}">
                                    Todos os Produtos
                                </a>
                            </li>
                            @foreach($categories as $cat)
                                <li>
                                    <a href="{{ route('shop.index', $cat->slug) }}"
                                       class="text-sm {{ request('slug') == $cat->slug ? 'text-[#B8860B] font-bold' : 'text-gray-600 hover:text-black transition-colors' }}">
                                        {{ $cat->nome }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                {{-- Conteúdo Principal --}}
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-10">
                        <h1 class="text-xl md:text-2xl font-black italic uppercase tracking-tighter text-gray-900">
                            @if(request('search'))
                                Busca: "{{ request('search') }}"
                            @elseif(request('slug'))
                                @php $currentCat = $categories->where('slug', request('slug'))->first(); @endphp
                                {{ $currentCat ? $currentCat->nome : 'Produtos' }}
                            @else
                                Loja K'enzza Professional
                            @endif
                        </h1>

                        <form action="{{ route('shop.index', request('slug')) }}" method="GET" id="sortForm">
                            @foreach(request()->except(['sort', 'slug']) as $k => $v)
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endforeach
                            <select name="sort" onchange="this.form.submit()" class="text-[10px] font-bold uppercase tracking-widest border-none bg-transparent focus:ring-0 cursor-pointer text-gray-600 hover:text-black">
                                <option value="">Ordenar</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Menor Preço</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Maior Preço</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Lançamentos</option>
                            </select>
                        </form>
                    </div>

                    {{-- Grid de Produtos --}}
                    <div id="product-grid-container" class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-3 gap-3 md:gap-8">
                        {{-- CORREÇÃO: Caminho do include atualizado --}}
                        @include('ecommerce.partials.product-cards', ['products' => $products])
                    </div>

                    {{-- Marcador do Scroll --}}
                    <div x-ref="infiniteMarker" class="w-full text-center py-12 mt-6">
                        <template x-if="loading">
                            <div class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-[#B8860B]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Carregando mais produtos...</span>
                            </div>
                        </template>

                        <template x-if="!hasMore && {{ $products->total() }} > 0">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-300">Você chegou ao fim do catálogo.</p>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-store-layout>
