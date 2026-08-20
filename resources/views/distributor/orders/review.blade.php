<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 uppercase tracking-tighter text-center">Revisar Pedido</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-2xl rounded-[2.5rem] border border-gray-100">
                <h3 class="font-black uppercase text-sm tracking-widest mb-6">Resumo dos Itens</h3>
                <div class="space-y-4 mb-8">
                    @foreach($summary as $item)
                    <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                        <span class="text-sm text-gray-600">{{ $item['qty'] }}x {{ $item['nome'] }}</span>
                        <span class="font-bold text-gray-900">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between items-center pt-4">
                        <span class="font-black uppercase text-lg">Total</span>
                        <span class="font-black text-2xl text-[#B8860B]">R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
                    </div>
                </div>

                <form action="{{ route('orders.storeOrder') }}" method="POST">
                    @csrf

                    {{-- CORREÇÃO: Inputs ocultos garantem que os itens selecionados viajem até o método storeOrder --}}
                    @foreach($summary as $item)
                        <input type="hidden" name="items[{{ $item['id'] }}]" value="{{ $item['qty'] }}">
                    @endforeach

                    <h3 class="font-black uppercase text-sm tracking-widest mb-4">Forma de Pagamento</h3>
                    <div class="grid grid-cols-1 gap-4 mb-8">
                        <label class="flex items-center p-4 border rounded-2xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="payment_method" value="pix" required class="text-[#B8860B] focus:ring-[#B8860B]">
                            <span class="ml-3 font-bold uppercase text-xs tracking-widest">Pix (Aprovação Imediata)</span>
                        </label>
                        <label class="flex items-center p-4 border rounded-2xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="payment_method" value="boleto" class="text-[#B8860B] focus:ring-[#B8860B]">
                            <span class="ml-3 font-bold uppercase text-xs tracking-widest">Boleto Bancário</span>
                        </label>
                        <label class="flex items-center p-4 border rounded-2xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="payment_method" value="cheque" class="text-[#B8860B] focus:ring-[#B8860B]">
                            <span class="ml-3 font-bold uppercase text-xs tracking-widest">Cheque / Faturamento</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-black text-[#B8860B] py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-[#B8860B] hover:text-black transition-all shadow-xl">
                        Confirmar e Finalizar Pedido
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
