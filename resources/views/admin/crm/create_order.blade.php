<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 uppercase tracking-tighter">Emissão de Pedido <span class="text-[#c5a059]">Manual</span></h2>
    </x-slot>

    @php
    $jsProducts = $products->map(function($p) {
        $val = $p->preco_distribuidor;
        $preco = is_string($val) && str_contains($val, ',') ? (float) str_replace(['.', ','], ['', '.'], $val) : (float) $val;
        return [ 'id' => (int)$p->id, 'preco' => $preco ];
    });
    @endphp

    <div class="py-6 sm:py-12" x-data="orderHandler()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form action="{{ route('crm.orders.store') }}" method="POST" class="relative bg-white shadow-xl rounded-t-[2.5rem] border border-gray-100 flex flex-col pb-64">
                @csrf

                {{-- TOPO FIXO: SELETOR DE CLIENTE E BUSCA --}}
                <div class="sticky top-0 z-40 bg-white/95 backdrop-blur-md p-4 sm:p-6 border-b border-gray-100 rounded-t-[2.5rem] shadow-sm flex flex-col gap-4">

                    {{-- Seleção do Distribuidor --}}
                    <div class="max-w-3xl mx-auto w-full">
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">1. Selecione o Cliente (Distribuidor)</label>
                        <select name="distributor_id" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-3 px-4 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-[#B8860B] focus:border-transparent cursor-pointer">
                            <option value="">-- Escolha na lista --</option>
                            @foreach($distributors as $distributor)
                                <option value="{{ $distributor->id }}" {{ request('distributor_id') == $distributor->id ? 'selected' : '' }}>
                                    {{ $distributor->name }} ({{ $distributor->city ?? 'S/Cidade' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Busca de Produtos --}}
                    <div class="relative max-w-3xl mx-auto w-full">
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">2. Busque os Produtos</label>
                        <div class="absolute inset-y-0 bottom-0 left-0 pl-4 flex items-center pointer-events-none pb-1">
                            <svg class="h-5 w-5 text-[#B8860B] mt-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchQuery" placeholder="Nome ou SKU..." class="w-full bg-white border border-gray-200 rounded-full pl-12 pr-4 py-3 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-[#B8860B] focus:border-transparent shadow-sm placeholder-gray-400">
                    </div>
                </div>

                {{-- CABEÇALHO DA LISTA --}}
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
                            $valorOriginal = $product->preco_distribuidor;
                            $precoLimpo = is_string($valorOriginal) && str_contains($valorOriginal, ',') ? (float) str_replace(['.', ','], ['', '.'], $valorOriginal) : (float) $valorOriginal;
                            $searchStr = mb_strtolower(preg_replace('/[^A-Za-z0-9 ]/', '', $product->nome . ' ' . ($product->sku ?? $product->id)), 'UTF-8');
                        @endphp

                        <div class="p-6 sm:px-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors"
                             data-search="{{ $searchStr }}"
                             x-show="searchQuery === '' || $el.dataset.search.includes(searchQuery.toLowerCase().replace(/[^a-z0-9 ]/g, ''))">

                            <div class="flex items-center gap-4 flex-1">
                                <img src="{{ asset('storage/' . $product->imagem) }}" class="w-16 h-16 sm:w-12 sm:h-12 rounded-xl object-contain bg-white border border-gray-100 p-1">
                                <div>
                                    <p class="font-black text-gray-900 text-sm sm:text-base leading-tight">{{ $product->nome }}</p>
                                    <p class="text-[10px] text-[#B8860B] font-black uppercase tracking-widest mt-1">SKU: {{ $product->sku ?? $product->id }} | Est: {{ $product->estoque }}</p>
                                    <p class="sm:hidden font-bold text-gray-600 text-sm mt-2">R$ {{ number_format($precoLimpo, 2, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="hidden sm:block text-center font-bold text-gray-600 text-sm w-32">
                                R$ {{ number_format($precoLimpo, 2, ',', '.') }}
                            </div>

                            <div class="flex items-center justify-between sm:justify-center w-full sm:w-32 mt-2 sm:mt-0">
                                <span class="sm:hidden text-[10px] font-black uppercase text-gray-500 tracking-widest">Qtd</span>
                                <input type="number" name="items[{{ $product->id }}]" x-model.number="items[{{ $product->id }}]" min="0" placeholder="0"
                                       class="w-20 text-center font-black border-gray-200 rounded-full text-sm focus:ring-[#B8860B] focus:border-[#B8860B] shadow-sm">
                            </div>

                            <div class="flex items-center justify-between sm:justify-end w-full sm:w-32">
                                <span class="sm:hidden text-[10px] font-black uppercase text-gray-400 tracking-widest">Subtotal</span>
                                <span class="font-black text-gray-900 text-lg sm:text-sm" x-text="formatMoney((items[{{ $product->id }}] || 0) * {{ $precoLimpo }})">
                                    R$ 0,00
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- RODAPÉ FIXO DE FECHAMENTO --}}
                <div class="fixed bottom-0 left-0 w-full z-50 bg-gray-900 border-t-4 border-[#B8860B] shadow-[0_-20px_40px_rgba(0,0,0,0.15)]">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col gap-4">

                        {{-- Entradas Manuais da Gerente --}}
                        <div class="flex flex-col sm:flex-row gap-4 border-b border-gray-700 pb-4">

                            {{-- Frete --}}
                            <div class="flex-1">
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-2 block">Custo de Frete (R$)</label>
                                <input type="number" step="0.01" min="0" name="frete" x-model.number="frete" placeholder="0.00" class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl focus:ring-[#B8860B] focus:border-[#B8860B] py-3 px-4">
                            </div>

                            {{-- SELEÇÃO DE PAGAMENTO (SELECT BOX) --}}
                            <div class="flex-1 relative">
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-2 block">Forma de Pagamento</label>
                                <select name="pagamento" required class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl py-3 px-4 font-bold focus:ring-[#B8860B] focus:border-[#B8860B] appearance-none cursor-pointer">
                                    <option value="" disabled selected>-- Selecione o método --</option>
                                    <option value="PIX">PIX (Aprovação Imediata)</option>
                                    <option value="Boleto Bancário">Boleto Bancário</option>
                                    <option value="Cheque / Faturamento">Cheque / Faturamento</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 top-6 flex items-center pr-4 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                        </div>

                        {{-- Total e Fechar Pedido --}}
                        <div class="flex flex-col sm:flex-row justify-between items-center text-white">
                            <div class="flex justify-between items-center w-full sm:w-auto mb-3 sm:mb-0">
                                <div>
                                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Total Final c/ Frete</p>
                                    <p class="text-3xl font-black text-[#B8860B]" x-text="formatMoney(totalGeral)"></p>
                                </div>
                            </div>

                            <button type="submit" x-show="totalGeral > 0" class="w-full sm:w-auto bg-[#B8860B] text-black px-8 py-4 rounded-full font-black uppercase tracking-[0.2em] hover:bg-white transition-all shadow-xl text-[10px] sm:text-xs">
                                Confirmar e Salvar Pedido
                            </button>
                        </div>
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
                frete: 0,
                products: @json($jsProducts),

                get totalGeral() {
                    let total = 0;
                    for (let id in this.items) {
                        let qty = parseFloat(this.items[id]) || 0;
                        let product = this.products.find(p => p.id == id);
                        if (product) total += product.preco * qty;
                    }
                    return total + (parseFloat(this.frete) || 0);
                },

                formatMoney(value) {
                    return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
</x-app-layout>
