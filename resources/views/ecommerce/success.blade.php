@if(isset($order))
<script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        'event': 'purchase',
        'ecommerce': {
            'transaction_id': '{{ $order->id }}',
            'value': {{ number_format($order->total, 2, '.', '') }},
            'tax': 0.00,
            'shipping': {{ number_format($order->shipping_cost ?? 0, 2, '.', '') }},
            'currency': 'BRL',
            'items': [
                @foreach($order->items as $item)
                {
                    'item_id': '{{ $item->product_id }}',
                    'item_name': '{{ $item->product->name ?? "Produto K’enzza" }}',
                    'price': {{ number_format($item->preco_unitario, 2, '.', '') }}, {{-- Ajustado para preco_unitario --}}
                    'quantity': {{ $item->quantidade }} {{-- Ajustado para quantidade --}}
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ]
        }
    });
</script>
@endif

<x-store-layout>
    <div class="bg-gray-50 min-h-screen py-20">
        <div class="max-w-3xl mx-auto px-6 text-center">

            <div class="bg-white p-12 rounded-[3rem] shadow-xl border border-gray-100">
                {{-- Ícone de Sucesso --}}
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h1 class="text-3xl font-black uppercase italic tracking-tighter mb-2">Pedido Recebido!</h1>
                <p class="text-gray-500 mb-8 font-medium">
                    O seu pedido <span class="text-black font-bold">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span> foi gerado com sucesso.
                </p>

                {{-- Seção PIX --}}
                @if($order->metodo_pagamento == 'pix')
                    <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 inline-block w-full">
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-[#B8860B] mb-6">Pague com PIX para agilizar</h2>

                        <div class="bg-white p-4 rounded-3xl inline-block shadow-sm mb-6 border border-gray-100">
                            {{-- QR Code --}}
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($pixPayload) }}"
                                 alt="QR Code PIX"
                                 class="rounded-xl">
                        </div>

                        <div class="space-y-4 text-left max-w-md mx-auto">
                            <label class="text-[10px] font-black uppercase text-gray-400 block ml-4 tracking-widest">Código Copia e Cola</label>
                            <div class="flex gap-2">
                                <input type="text" id="pixCode" value="{{ $pixPayload }}" readonly
                                    class="flex-1 bg-white border-none rounded-2xl text-xs p-4 focus:ring-1 focus:ring-[#B8860B] shadow-sm">
                                <button onclick="copyPix()"
                                    class="bg-black text-white px-6 rounded-2xl font-black text-[10px] uppercase hover:bg-[#B8860B] transition-all active:scale-95 shadow-lg">
                                    Copiar
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Botões de Navegação --}}
                <div class="mt-12 flex flex-col md:flex-row gap-4 justify-center">
                    <a href="{{ route('customer.orders') }}"
                       class="bg-black text-white px-10 py-5 rounded-full font-black uppercase text-[10px] tracking-[0.2em] hover:bg-[#B8860B] hover:text-black transition-all shadow-xl active:scale-95">
                        Ver Meus Pedidos
                    </a>
                    <a href="{{ route('shop.home') }}"
                       class="text-gray-400 px-10 py-5 rounded-full font-black uppercase text-[10px] tracking-[0.2em] hover:text-black transition-all">
                        Voltar para a Loja
                    </a>
                </div>
            </div>

            <p class="mt-10 text-gray-400 text-[10px] font-bold uppercase tracking-widest">
                Você receberá uma confirmation no e-mail: <span class="text-gray-600">{{ auth()->user()->email }}</span>
            </p>
        </div>
    </div>

    {{-- Script de Cópia --}}
    <script>
    function copyPix() {
        var copyText = document.getElementById("pixCode");
        copyText.select();
        copyText.setSelectionRange(0, 99999);

        try {
            navigator.clipboard.writeText(copyText.value);
            alert("Código PIX copiado com sucesso! Agora é só colar no seu app do banco.");
        } catch (err) {
            // Fallback para navegadores antigos
            document.execCommand("copy");
            alert("Código PIX copiado!");
        }
    }
    </script>
</x-store-layout>
