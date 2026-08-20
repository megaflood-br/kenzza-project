@section('title', $product->nome)
@section('seo')
    <meta name="description" content="{{ Str::limit(strip_tags($product->descricao), 160) }}">

    {{-- Schema.org para Google Merchant Center com @@ para não conflitar com o Laravel --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org/",
      "@@type": "Product",
      "name": "{{ $product->nome }}",
      "image": "{{ asset('storage/' . $product->imagem) }}",
      "description": "{{ Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($product->descricao))), 150) }}",
      "offers": {
        "@@type": "Offer",
        "priceCurrency": "BRL",
        "price": "{{ $product->preco_atual }}",
        "availability": "{{ $product->estoque > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
        "url": "{{ request()->fullUrl() }}"
      }
    }
    </script>
@endsection

<x-store-layout>
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">

            {{-- Breadcrumb --}}
            <nav class="flex mb-8 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                <a href="{{ route('shop.index') }}" class="hover:text-black transition">Loja</a>
                <span class="mx-3 text-gray-200">/</span>
                <span class="text-gray-900">{{ $product->category->nome ?? 'Produto' }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

                {{-- COLUNA DA ESQUERDA: Imagem + Ficha Técnica --}}
                <div class="flex flex-col gap-6">
                    {{-- Imagem do Produto --}}
                    <div class="bg-white rounded-[3rem] p-10 flex items-center justify-center border border-gray-100 shadow-sm overflow-hidden group min-h-[500px]">
                        @if($product->imagem)
                            <img src="{{ asset('storage/' . $product->imagem) }}"
                                 alt="{{ $product->nome }}"
                                 class="max-h-[500px] w-auto object-contain transition-transform duration-700 group-hover:scale-105">
                        @endif
                    </div>

                    {{-- FICHA TÉCNICA DO PRODUTO (Movida para debaixo da imagem) --}}
                    @if($product->ficha)
                        <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm text-center">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Informações e Especificações Profissionais</p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="{{ route('sheets.show.public', $product->ficha->id) }}"
                                   target="_blank"
                                   class="flex-1 flex items-center justify-center bg-white border-2 border-[#B8860B] text-[#B8860B] px-8 py-4 rounded-full font-black hover:bg-[#B8860B] hover:text-white transition-all uppercase tracking-[0.2em] text-xs shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Ver Ficha Técnica
                                </a>

                                @if($product->ficha->pdf)
                                    <a href="{{ asset('storage/' . $product->ficha->pdf) }}"
                                       target="_blank"
                                       class="flex items-center justify-center bg-red-50 border-2 border-red-100 text-red-600 px-6 py-4 rounded-full font-black hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                       title="Baixar PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- COLUNA DA DIREITA: Detalhes, Preço e Compra --}}
                <div class="flex flex-col pt-4">
                    <h3 class="text-[10px] font-black text-[#B8860B] uppercase tracking-[0.4em] mb-4">
                        {{ $product->category->nome ?? "K'enzza Professional" }}
                    </h3>
                    <h1 class="text-5xl font-black text-gray-900 mb-6 tracking-tighter uppercase leading-tight">{{ $product->nome }}</h1>

                    <div class="flex items-center gap-6 mb-10">
                        <span class="text-4xl font-black text-gray-900 tracking-tighter">
                            R$ {{ number_format($product->preco_atual, 2, ',', '.') }}
                        </span>
                        <div class="h-8 w-px bg-gray-100"></div>
                        <span class="text-[10px] text-gray-400 uppercase font-black tracking-widest leading-none">Em até 12x no cartão</span>
                    </div>

                    <div class="prose prose-sm text-gray-600 mb-10 font-normal leading-relaxed text-base max-w-none">
                        {!! $product->descricao !!}
                    </div>

                    {{-- SIMULADOR DE FRETE --}}
                    <div class="mb-10 p-8 bg-gray-50/50 rounded-[2rem] border border-gray-100"
                         x-data="{
                            cepExibicao: '{{ $cep }}',
                            productId: '{{ $product->id }}',
                            shippingOptions: [],
                            loading: false,
                            error: null,

                            aplicarMascara(valor) {
                                valor = valor.replace(/\D/g, '');
                                if (valor.length > 5) {
                                    valor = valor.replace(/^(\d{5})(\d)/, '$1-$2');
                                }
                                this.cepExibicao = valor;
                            },

                            async calculate() {
                                let cepLimpo = this.cepExibicao.replace(/\D/g, '');
                                if(cepLimpo.length < 8) return;

                                this.loading = true;
                                this.error = null;
                                this.shippingOptions = [];

                                try {
                                    const response = await fetch(`{{ route('product.frete') }}?cep=${cepLimpo}&product_id=${this.productId}`);
                                    const data = await response.json();

                                    if(data.sucesso) {
                                        this.shippingOptions = data.opcoes;
                                    } else {
                                        this.error = data.erro || 'Não foi possível calcular o frete.';
                                    }
                                } catch (e) {
                                    console.error('Erro na requisição de frete:', e);
                                    this.error = 'Erro na conexão com o servidor.';
                                } finally {
                                    this.loading = false;
                                }
                            }
                         }" x-init="if(cepExibicao.length >= 8) calculate()">

                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Calcular Frete e Prazo</label>
                        <div class="flex gap-2">
                            <input type="text"
                                   x-model="cepExibicao"
                                   x-on:input="aplicarMascara($event.target.value)"
                                   maxlength="9"
                                   placeholder="00000-000"
                                   class="flex-1 bg-white border-gray-200 rounded-full px-3 py-3 text-sm focus:ring-1 focus:ring-[#B8860B] shadow-sm">

                            <button x-on:click="calculate()" type="button"
                                    class="bg-black text-white px-5 py-3 rounded-full text-[10px] font-black uppercase hover:bg-[#B8860B] transition-all flex items-center justify-center min-w-[90px]">
                                <span x-show="!loading">Calcular</span>
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>

                        <p x-show="error" x-text="error" class="text-[10px] text-red-500 font-bold uppercase mt-4 tracking-tighter"></p>

                        <div x-show="shippingOptions.length > 0" x-transition class="mt-6 space-y-3">
                            <template x-for="(opt, i) in shippingOptions" :key="i">
                                <div class="flex justify-between items-center text-sm p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                                    <div>
                                        <span class="block font-bold text-gray-900" x-text="opt.nome"></span>
                                        <span class="text-[9px] text-gray-400 uppercase font-black" x-text="opt.prazo"></span>
                                    </div>
                                    <span class="font-black text-[#B8860B]" x-text="'R$ ' + parseFloat(opt.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- BOTÕES DE AÇÃO --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <form action="{{ route('cart.add', $product->slug) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-[#B8860B] text-white px-8 py-6 rounded-full font-black hover:bg-black transition-all shadow-xl shadow-gold-500/20 uppercase tracking-[0.2em] text-xs">
                                Adicionar ao Carrinho
                            </button>
                        </form>

                        <a href="https://wa.me/5511912443903"
                           target="_blank"
                           class="flex-1 flex items-center justify-center bg-white border-2 border-gray-100 text-gray-900 px-8 py-6 rounded-full font-black hover:bg-gray-50 transition-all uppercase tracking-[0.2em] text-xs">
                            Dúvidas? WhatsApp
                        </a>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-6 pt-8 border-t border-gray-100">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-[10px] font-black uppercase text-gray-500 tracking-widest">Envio Imediato</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-[10px] font-black uppercase text-gray-500 tracking-widest">Qualidade Profissional</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PRODUTOS RELACIONADOS --}}
            @if(isset($relatedProducts) && count($relatedProducts) > 0)
                <div class="mt-40 pt-20 border-t border-gray-100">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-black text-gray-900 italic uppercase tracking-tighter">
                            Quem viu este, <span class="text-[#B8860B]">também amou</span>
                        </h2>
                        <div class="h-1 w-24 bg-[#B8860B] mx-auto mt-6"></div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-10">
                        @foreach($relatedProducts as $related)
                            <div class="group flex flex-col">
                                <a href="{{ route('shop.product', $related->slug) }}" class="block bg-white border border-gray-100 rounded-[2rem] p-6 aspect-square flex items-center justify-center overflow-hidden transition-all hover:shadow-2xl">
                                    <img src="{{ asset('storage/' . $related->imagem) }}" class="max-h-full w-auto object-contain transition-transform duration-700 group-hover:scale-110">
                                </a>
                                <div class="mt-6 text-center">
                                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-tighter line-clamp-1 px-2">{{ $related->nome }}</h3>
                                    <p class="text-[#B8860B] font-black text-lg mt-1 tracking-tighter">
                                        R$ {{ number_format($related->preco_atual, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-store-layout>
