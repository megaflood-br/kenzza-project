<x-store-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Cabeçalho do Pedido --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
                <div>
                    <a href="{{ route('customer.orders') }}" class="text-[10px] font-black text-[#B8860B] uppercase tracking-[0.2em] hover:underline flex items-center gap-2 mb-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                        Voltar aos Pedidos
                    </a>
                    <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Detalhes do Pedido <span class="text-[#B8860B]">#{{ $order->id }}</span></h1>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Realizado em {{ $order->created_at->format('d/m/Y à\s H:i') }}</p>
                </div>

                <div class="px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest
                    {{ $order->status === 'pago' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                    Status: {{ strtoupper($order->status) }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Itens do Pedido --}}
                <div class="md:col-span-2 space-y-4">
                    <div class="bg-[#fcfcfc] border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em] mb-6">Produtos</h3>

                        <div class="divide-y divide-gray-100">
                            {{-- Removido o ?? [] pois o with(['items']) garante a coleção --}}
                            @forelse($order->items as $item)
                                <div class="py-4 flex items-center gap-4">
                                    {{-- Imagem do Produto --}}
                                    <div class="w-16 h-16 bg-white rounded-2xl border border-gray-50 flex-shrink-0 overflow-hidden">
                                        @if($item->product && $item->product->imagem)
                                            <img src="{{ asset('storage/' . $item->product->imagem) }}" class="w-full h-full object-contain p-2">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-50">
                                                <span class="text-[8px] text-gray-300">SEM FOTO</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Detalhes --}}
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900">
                                            {{ $item->product->nome ?? 'Produto não disponível' }} {{-- Uso do campo 'nome' --}}
                                        </p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase">Qtd: {{ $item->quantidade }}</p>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm font-black text-gray-900">
                                            R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-400 italic text-sm">Nenhum item listado.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Resumo e Pagamento --}}
                <div class="space-y-6">
                    {{-- Card Totais --}}
                    <div class="bg-black text-white p-8 rounded-[2.5rem] shadow-xl">
                        <h3 class="text-[10px] font-black text-[#B8860B] uppercase tracking-[0.2em] mb-6">Resumo</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Método:</span>
                                <span class="font-bold uppercase">{{ $order->metodo_pagamento }}</span>
                            </div>
                            <div class="border-t border-white/10 pt-3 flex justify-between items-center">
                                <span class="text-sm font-bold">TOTAL</span>
                                <span class="text-xl font-black text-[#B8860B]">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                            </div>

                            {{-- Link de Pagamento para Pedidos Pendentes --}}
                            @if($order->status === 'pendente')
                                <div class="mt-8 pt-8 border-t border-white/5">
                                    @php
                                        $urlPagamento = '#';

                                        // Verifica se temos o ID externo do Asaas salvo no pedido
                                        if (!empty($order->external_id)) {
                                            $asaasKey = env('ASAAS_API_KEY');
                                            $asaasUrl = env('ASAAS_ENV') === 'production' ? 'https://www.asaas.com/api/v3' : 'https://sandbox.asaas.com/api/v3';

                                            // Consulta a API do Asaas e salva em cache por 1 hora para não deixar a tela lenta
                                            $urlPagamento = \Illuminate\Support\Facades\Cache::remember('asaas_url_' . $order->external_id, 3600, function() use ($asaasUrl, $asaasKey, $order) {
                                                try {
                                                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                                                        'access_token' => $asaasKey
                                                    ])->get("{$asaasUrl}/payments/{$order->external_id}");

                                                    if ($response->successful()) {
                                                        return $response->json()['invoiceUrl'] ?? '#';
                                                    }
                                                } catch (\Exception $e) {
                                                    return '#';
                                                }
                                                return '#';
                                            });
                                        }
                                    @endphp

                                    <div class="mb-4">
                                        <span class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em]">Aguardando Pagamento</span>
                                    </div>

                                    @if($urlPagamento !== '#')
                                        <a href="{{ $urlPagamento }}"
                                           target="_blank"
                                           class="group relative flex items-center justify-center w-full bg-[#B8860B] hover:bg-white text-black py-5 rounded-[1.5rem] transition-all duration-300 shadow-lg overflow-hidden">

                                            {{-- Texto do Botão --}}
                                            <span class="relative z-10 text-[10px] font-black uppercase tracking-[0.15em] group-hover:text-black transition-colors">
                                                Finalizar Pagamento
                                            </span>

                                            {{-- Ícone sutil à direita --}}
                                            <svg class="relative z-10 w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                            </svg>
                                        </a>

                                        <p class="mt-4 text-[8px] text-center text-gray-400 font-bold uppercase tracking-widest leading-relaxed">
                                            Clique acima para acessar a fatura segura via Asaas
                                        </p>
                                    @else
                                        <p class="text-[10px] text-red-400 font-bold uppercase text-center mt-4">
                                            Link de pagamento indisponível no momento.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Card Rastreio (se houver) --}}
                    @if($order->codigo_rastreio)
                        <div class="bg-gray-50 border border-gray-100 p-8 rounded-[2.5rem]">
                            <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-[0.2em] mb-4">Rastreamento</h3>

                            <p class="text-xs font-bold text-[#B8860B] mb-2">
                                Código: {{ $order->codigo_rastreio }}
                            </p>

                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-4">
                                Status: {{ $order->status_envio ?? 'Em processamento' }}
                            </p>

                            <a href="https://www.melhorrastreio.com.br/rastreio/{{ $order->codigo_rastreio }}"
                               target="_blank"
                               class="inline-block text-[9px] font-black bg-white border border-gray-200 px-6 py-3 rounded-full hover:bg-black hover:text-white transition-all uppercase tracking-widest">
                               Acompanhar no Mapa
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-store-layout>
