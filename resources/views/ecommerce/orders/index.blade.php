@extends('layouts.store')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-5xl mx-auto px-6 lg:px-10">

        <div class="flex items-center justify-between mb-10">
            <h1 class="text-3xl font-black italic uppercase tracking-tighter text-black">Meus Pedidos</h1>
            <a href="{{ route('shop.index') }}" class="text-[10px] font-bold uppercase tracking-widest text-[#B8860B] hover:underline">Continuar Comprando</a>
        </div>

        @forelse($orders as $order)
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 mb-6 overflow-hidden">
                <div class="bg-gray-50/50 px-8 py-6 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex gap-8">
                        <div>
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Pedido</p>
                            <p class="text-sm font-bold text-gray-900">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Data</p>
                            <p class="text-sm font-bold text-gray-900">{{ $order->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Total</p>
                            <p class="text-sm font-bold text-[#B8860B]">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest
                            @if($order->status == 'pendente') bg-yellow-100 text-yellow-700 @endif
                            @if($order->status == 'aprovado' || $order->status == 'enviado') bg-green-100 text-green-700 @endif
                            @if($order->status == 'cancelado') bg-red-100 text-red-700 @endif">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100">
                                        <img src="{{ asset('storage/' . $item->product->imagem) }}" class="max-h-10 object-contain">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-gray-900">{{ $item->product->nome }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase font-black">{{ $item->quantidade }}x R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="bg-gray-50 rounded-[2rem] p-6 border border-gray-100">
                            @if($order->status == 'enviado' && $order->codigo_rastreio)
                                <h3 class="text-[10px] font-black uppercase text-[#B8860B] tracking-widest mb-3">Objeto Postado</h3>
                                <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-gray-200">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Código de Rastreio</p>
                                        <p class="text-sm font-black text-black uppercase tracking-widest">{{ $order->codigo_rastreio }}</p>
                                    </div>
                                    <a href="https://rastreamento.correios.com.br/app/index.php?objeto={{ $order->codigo_rastreio }}" target="_blank"
                                       class="bg-black text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-[#B8860B] transition">
                                        Rastrear
                                    </a>
                                </div>
                            @elseif($order->status == 'cancelado')
                                <p class="text-xs text-red-500 font-bold italic">Este pedido foi cancelado. Entre em contato com o suporte para mais informações.</p>
                            @else
                                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-3">Status da Entrega</h3>
                                <p class="text-xs text-gray-600 leading-relaxed font-medium">
                                    Seu pedido está em fase de <span class="text-black font-bold uppercase">{{ str_replace('_', ' ', $order->status) }}</span>.
                                    Assim que for enviado, o código de rastreio aparecerá aqui.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-white rounded-[3rem] border border-gray-100 shadow-sm">
                <p class="text-gray-400 mb-6 font-medium italic">Você ainda não realizou nenhum pedido.</p>
                <a href="{{ route('shop.index') }}" class="bg-black text-white px-10 py-4 rounded-full font-black uppercase text-[10px] tracking-[0.2em] hover:bg-[#B8860B] transition-all">Começar a Comprar</a>
            </div>
        @endforelse

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
