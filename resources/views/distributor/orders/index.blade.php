<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-gray-800 uppercase tracking-tighter text-[#c5a059]">Meus Pedidos</h2>

            <a href="{{ route('orders.create') }}" class="bg-black text-[#c5a059] px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-xl">
                + Novo Pedido
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ openModal: false, selectedOrder: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- MENSAGEM DE SUCESSO / ALERTA --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-8 bg-black/5 border border-[#c5a059] px-6 py-4 rounded-[2rem] flex justify-between items-center shadow-sm backdrop-blur-sm">
                    <div class="flex items-center gap-4">
                        <div class="bg-[#c5a059] text-black p-2 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="font-bold text-sm text-gray-800">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-[#c5a059] transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-gray-100">
                <div class="p-8">
                    @if($orders->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-400 font-bold uppercase text-xs tracking-widest italic">Você ainda não realizou nenhum pedido.</p>
                            <a href="{{ route('orders.create') }}" class="inline-block mt-4 text-[#c5a059] font-black uppercase text-[10px] hover:underline">
                                Iniciar primeiro pedido agora
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">ID</th>
                                        <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Data</th>
                                        <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Status</th>
                                        <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest text-right">Total</th>
                                        <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                            <td class="py-4 px-4 font-bold text-sm">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td class="py-4 px-4 text-xs text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter
                                                    @if($order->status == 'pendente') bg-yellow-100 text-yellow-700 @endif
                                                    @if($order->status == 'aprovado') bg-green-100 text-green-700 @endif
                                                    @if($order->status == 'enviado') bg-blue-100 text-blue-700 @endif
                                                    @if($order->status == 'cancelado') bg-red-100 text-red-700 @endif">
                                                    {{ str_replace('_', ' ', $order->status) }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-right font-black text-sm">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                                            <td class="py-4 px-4 text-center">
                                                <button
                                                    @click="selectedOrder = {{ json_encode($order->load('items.product')) }}; openModal = true"
                                                    class="text-black hover:text-[#c5a059] transition p-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-8">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-md" @click="openModal = false"></div>
            <div x-show="openModal" x-transition class="bg-white rounded-[3rem] shadow-2xl w-full max-w-2xl relative z-10 overflow-hidden flex flex-col max-h-[90vh]">

                <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-white">
                    <h3 class="font-black text-xl uppercase italic">Detalhes do Pedido #<span x-text="String(selectedOrder.id).padStart(5, '0')"></span></h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-black text-2xl">&times;</button>
                </div>

                <div class="p-8 overflow-y-auto flex-1">
                    <div class="space-y-4">
                        <template x-for="item in selectedOrder.items" :key="item.id">
                            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-gray-200 font-black text-xs" x-text="item.quantidade + 'x'"></div>
                                    <span class="font-bold text-sm text-gray-900" x-text="item.product?.nome || 'Produto Indisponível'"></span>
                                </div>
                                <span class="font-black text-sm" x-text="'R$ ' + parseFloat(item.subtotal).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                            </div>
                        </template>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5">
                            <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-3">Forma de Pagamento</h4>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="w-4 h-4 text-[#c5a059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <span class="font-bold text-sm text-gray-900 uppercase" x-text="
                                    selectedOrder.metodo_pagamento === 'pix' ? 'Pix (Aprovação Imediata)' :
                                    (selectedOrder.metodo_pagamento === 'boleto' ? 'Boleto Bancário' :
                                    (selectedOrder.metodo_pagamento === 'cheque' ? 'Cheque / Faturamento' : (selectedOrder.metodo_pagamento || 'A Combinar')))
                                "></span>
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5">
                            <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-3">Dados Cadastrais</h4>
                            <div class="space-y-1">
                                <p class="font-bold text-sm text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">CPF/CNPJ: {{ auth()->user()->document }}</p>
                                <p class="text-xs text-gray-500">Tel: {{ auth()->user()->phone }}</p>
                                <p class="text-[10px] text-gray-400 mt-2 leading-relaxed">
                                    {{ auth()->user()->street }}, {{ auth()->user()->number }} {{ auth()->user()->complement ? '- ' . auth()->user()->complement : '' }}<br>
                                    {{ auth()->user()->neighborhood }} - {{ auth()->user()->city }}/{{ auth()->user()->state }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <template x-if="selectedOrder.codigo_rastreio">
                        <div class="mt-6 p-6 bg-blue-50 rounded-[2rem] border border-blue-100">
                            <p class="text-[10px] font-black uppercase text-blue-400 tracking-widest mb-2">Código de Rastreio</p>
                            <div class="flex justify-between items-center">
                                <span class="font-black text-blue-900 uppercase tracking-widest" x-text="selectedOrder.codigo_rastreio"></span>
                                <a :href="'https://rastreamento.correios.com.br/app/index.php?objeto=' + selectedOrder.codigo_rastreio" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded-xl font-bold text-[10px] uppercase">Rastrear</a>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="p-8 bg-gray-50 flex justify-between items-center border-t border-gray-100">
                    <span class="text-xs font-bold text-gray-500 uppercase">Valor Total</span>
                    <span class="text-2xl font-black text-[#c5a059]" x-text="'R$ ' + parseFloat(selectedOrder.total || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
