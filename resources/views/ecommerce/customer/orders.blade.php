<x-store-layout>
    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-4xl font-black text-black uppercase tracking-tighter">
                    Meus <span class="text-[#B8860B]">Pedidos</span>
                </h2>
                <div class="h-1 w-20 bg-[#B8860B] mt-2"></div>
            </div>

            <div class="space-y-6">
                @forelse($orders as $order)
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-wrap justify-between items-center hover:shadow-xl transition-all">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pedido #{{ $order->id }}</p>
                            <p class="text-lg font-bold text-black">{{ $order->created_at->format('d/m/Y') }}</p>
                        </div>

                        <div class="px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-600">
                            {{ $order->status }}
                        </div>

                        <div class="text-right">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</p>
                            <p class="text-2xl font-black text-black">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                        </div>

                        <a href="{{ route('customer.orders.show', $order) }}" class="bg-black text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-[#B8860B] transition-all">
                            Ver Detalhes
                        </a>
                    </div>
                @empty
                    <div class="text-center py-32 bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Você ainda não realizou nenhum pedido.</p>
                        <a href="{{ route('shop.home') }}" class="mt-8 inline-block text-[#B8860B] font-black text-xs uppercase tracking-widest hover:underline">Ir para a loja</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-store-layout>
