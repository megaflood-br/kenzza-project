<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 uppercase tracking-tighter">Novo Pedido em Lote</h2>
    </x-slot>

    @php
    $jsProducts = $products->map(function($p) {
        // Usa o preço de distribuidor!
        $val = $p->preco_distribuidor;

        if (is_string($val)) {
            // Se tiver vírgula, identifica como formato BR, senão trata como decimal puro
            $preco = str_contains($val, ',')
                ? (float) str_replace(['.', ','], ['', '.'], $val)
                : (float) $val;
        } else {
            $preco = (float) $val;
        }

        return [
            'id' => (int)$p->id,
            'preco' => $preco
        ];
    });
    @endphp

    {{-- Inicializa o AlpineJS --}}
    <div class="py-6 sm:py-12" x-data="orderHandler()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Formulário principal: Removido o overflow-hidden e adicionado padding inferior (pb-40) --}}
            <form action="{{ route('orders.review') }}" method="POST" class="relative bg-white shadow-xl rounded-t-[2.5rem] border border-gray-100 flex flex-col pb-40">
                @csrf

                {{-- BARRA DE PESQUISA FIXA NO TOPO --}}
                <div class="sticky top-0 z-40 bg-white/95 backdrop-blur-md p-4 sm:p-6 border-b border-gray-100 rounded-t-[2.5rem] shadow-sm">
                    <div class="relative max-w-2xl mx-auto">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-[#B8860B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="Buscar produto pelo nome ou SKU..."
                               class="w-full bg-gray-50 border border-gray-200 rounded-full pl-12 pr-4 py-3 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#B8860B] focus:border-transparent shadow-sm transition-all placeholder-gray-400">
                    </div>
                </div>

                {{-- CABEÇALHO DA LISTA (Aparece só no Desktop) --}}
                <div class="hidden sm:flex bg-gray-50 border-b border-gray-100 px-8 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">
                    <div class="flex-1">Produto</div>
                    <div class="w-32 text-center">Preço Un.</div>
                    <div class="w-32 text-center">Quantidade</div>
                    <div class="w-32 text-right">Subtotal</div>
                </div>

                {{-- LISTA DE PRODUTOS RESPONSIVA --}}
                <div class="divide-y divide-gray-50">
                    @foreach($products as $product)
                        @php
                            // Lógica do preço unitário
                            $valorOriginal = $product->preco_distribuidor;
                            if (is_string($valorOriginal) && str_contains($valorOriginal, ',')) {
                                $precoLimpo = (float) str_replace(['.', ','], ['', '.'], $valorOriginal);
                            } else {
                                $precoLimpo = (float) $valorOriginal;
                            }

                            // String higienizada para a busca no JS
                            $searchStr = mb_strtolower(preg_replace('/[^A-Za-z0-9 ]/', '', $product->nome . ' ' . ($product->sku ?? $product->id)), 'UTF-8');
                        @endphp

                        {{-- Item do Produto --}}
                        <div class="p-6 sm:px-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors"
                             data-search="{{ $searchStr }}"
                             x-show="searchQuery === '' || $el.dataset.search.includes(searchQuery.toLowerCase().replace(/[^a-z0-9 ]/g, ''))"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0">

                            {{-- Info do Produto (Imagem + Nome) --}}
                            <div class="flex items-center gap-4 flex-1">
                                <img src="{{ asset('storage/' . $product->imagem) }}" class="w-20 h-20 sm:w-14 sm:h-14 rounded-2xl object-contain bg-white p-1 border border-gray-100 shadow-sm">
                                <div>
                                    <p class="font-black text-gray-900 text-sm sm:text-base leading-tight">{{ $product->nome }}</p>
                                    <p class="text-[10px] text-[#B8860B] font-black uppercase tracking-widest mt-1">SKU: {{ $product->sku ?? $product->id }}</p>

                                    {{-- Preço no Mobile (Aparece debaixo do nome) --}}
                                    <p class="sm:hidden font-bold text-gray-600 text-sm mt-2">
                                        R$ {{ number_format($precoLimpo, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Preço no Desktop --}}
                            <div class="hidden sm:block text-center font-bold text-gray-600 text-sm w-32">
                                R$ {{ number_format($precoLimpo, 2, ',', '.') }}
                            </div>

                            {{-- Input de Quantidade --}}
                            <div class="flex items-center justify-between sm:justify-center w-full sm:w-32 bg-gray-50 sm:bg-transparent p-3 sm:p-0 rounded-2xl border border-gray-100 sm:border-0 mt-2 sm:mt-0">
                                <span class="sm:hidden text-[10px] font-black uppercase text-gray-500 tracking-widest">Quantidade</span>
                                <input type="number"
                                       name="items[{{ $product->id }}]"
                                       x-model.number="items[{{ $product->id }}]"
                                       min="0"
                                       placeholder="0"
                                       class="w-20 text-center font-black border-gray-200 rounded-full text-sm focus:ring-[#B8860B] focus:border-[#B8860B] shadow-sm">
                            </div>

                            {{-- Subtotal --}}
                            <div class="flex items-center justify-between sm:justify-end w-full sm:w-32 pt-2 sm:pt-0">
                                <span class="sm:hidden text-[10px] font-black uppercase text-gray-400 tracking-widest">Subtotal</span>
                                <span class="font-black text-gray-900 text-lg sm:text-sm" x-text="formatMoney((items[{{ $product->id }}] || 0) * {{ $precoLimpo }})">
                                    R$ 0,00
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- TOTAL E BOTÃO FIXOS NA TELA (Muda para "fixed bottom-0 w-full") --}}
                <div class="fixed bottom-0 left-0 w-full z-50 bg-gray-900 border-t-4 border-[#B8860B] shadow-[0_-20px_40px_rgba(0,0,0,0.15)]">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 flex flex-col sm:flex-row justify-between items-center text-white">

                        <div class="flex justify-between items-center w-full sm:w-auto mb-3 sm:mb-0">
                            <div>
                                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Total do Pedido</p>
                                <p class="text-3xl font-black text-[#B8860B]" x-text="formatMoney(totalGeral)"></p>
                            </div>
                            {{-- Contador de itens --}}
                            <div class="sm:hidden text-right" x-show="totalGeral > 0" x-transition>
                                <span class="bg-[#B8860B] text-black text-[10px] font-black uppercase px-3 py-1.5 rounded-full tracking-widest">
                                    <span x-text="Object.values(items).filter(v => v > 0).length"></span> itens
                                </span>
                            </div>
                        </div>

                        <button type="submit"
                                x-show="totalGeral > 0"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="w-full sm:w-auto bg-[#B8860B] text-black px-8 py-4 rounded-full font-black uppercase tracking-[0.2em] hover:bg-white transition-all shadow-xl text-[10px] sm:text-xs flex items-center justify-center gap-3">
                            Revisar e Pagar
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function orderHandler() {
            return {
                searchQuery: '',
                items: {},
                products: @json($jsProducts),

                get totalGeral() {
                    let total = 0;
                    for (let id in this.items) {
                        let qty = parseFloat(this.items[id]) || 0;
                        let product = this.products.find(p => p.id == id);
                        if (product) total += product.preco * qty;
                    }
                    return total;
                },

                formatMoney(value) {
                    return 'R$ ' + value.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
        }
    </script>
</x-app-layout>
