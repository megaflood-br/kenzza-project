<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hide-on-print">
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                Gestão de <span class="text-[#c5a059]">Pedidos</span>
            </h2>

            {{-- Filtro de Origem (Abas) --}}
            <div class="flex bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm w-full md:w-auto overflow-x-auto">

                {{-- Se for Gerente, mostra apenas a aba Representantes travada --}}
                @if(auth()->user()->role === 'manager')
                    <span class="flex-1 text-center whitespace-nowrap px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-black text-[#c5a059] shadow-md cursor-default">
                        Representantes
                    </span>
                @else
                    {{-- Se for Admin, mostra todas as abas --}}
                    <a href="{{ route('admin.orders.index') }}"
                        class="flex-1 text-center whitespace-nowrap px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                        {{ !request('origin') ? 'bg-black text-[#c5a059] shadow-md' : 'text-gray-500 hover:bg-gray-50' }}">
                        Todos
                    </a>
                    <a href="{{ route('admin.orders.index', ['origin' => 'ecommerce']) }}"
                        class="flex-1 text-center whitespace-nowrap px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                        {{ request('origin') === 'ecommerce' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50' }}">
                        E-commerce
                    </a>
                    <a href="{{ route('admin.orders.index', ['origin' => 'distributor']) }}"
                        class="flex-1 text-center whitespace-nowrap px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                        {{ request('origin') === 'distributor' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50' }}">
                        Distribuidores
                    </a>
                    {{-- Nova aba de Representantes para o Admin --}}
                    <a href="{{ route('admin.orders.index', ['origin' => 'representative']) }}"
                        class="flex-1 text-center whitespace-nowrap px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                        {{ request('origin') === 'representative' ? 'bg-green-700 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50' }}">
                        Representantes
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- ÁREA DE EXIBIÇÃO DE ALERTAS ADMINISTRATIVOS --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 hide-on-print">
        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-xs font-black uppercase tracking-widest text-green-800 bg-green-50 border border-green-200 rounded-2xl shadow-sm">
                <svg class="w-4 h-4 mr-2 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center p-4 mb-4 text-xs font-black uppercase tracking-widest text-red-800 bg-red-50 border border-red-200 rounded-2xl shadow-sm">
                <svg class="w-4 h-4 mr-2 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>{!! session('error') !!}</div>
            </div>
        @endif
    </div>

    {{-- Função getShippingName traduz os IDs do Melhor Envio --}}
    <div class="py-12 bg-gray-50 min-h-screen reset-on-print" x-data="{
        openModal: false,
        selectedOrder: {},
        getShippingName(id) {
            const map = {
                '1': 'Correios PAC',
                '2': 'Correios SEDEX',
                '3': 'Jadlog Package',
                '4': 'Jadlog .Com',
                '17': 'Correios Mini Envios',
                '31': 'Loggi',
                '33': 'LATAM Cargo'
            };
            return map[String(id)] || 'Serviço ID: ' + id;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reset-on-print">

            {{-- LISTA DE PEDIDOS - COMPLETAMENTE OCULTA NA IMPRESSÃO --}}
            <div class="hide-on-print bg-white shadow-sm sm:rounded-[2rem] border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="hidden lg:table-header-group bg-gray-50">
                        <tr class="border-b border-gray-100">
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">Cliente / Pedido</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Origem</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Data</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Status</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-right">Total</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody class="flex flex-col lg:table-row-group divide-y lg:divide-y-0 divide-gray-100 p-4 lg:p-0">
                        @forelse($orders as $order)
                            <tr class="flex flex-col lg:table-row bg-white lg:hover:bg-gray-50 transition-all p-4 lg:p-0 rounded-2xl lg:rounded-none border lg:border-0 border-gray-100 lg:border-b mb-4 lg:mb-0 shadow-sm lg:shadow-none">

                                {{-- 1. Cliente e ID --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Cliente</span>
                                    <div class="text-right lg:text-left">
                                        <p class="font-bold text-sm lg:text-base text-gray-900 leading-tight">
                                            {{ $order->user ? $order->user->name : 'Cliente ' . ucfirst($order->origin ?? 'Marketplace') }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mt-0.5">ID #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </td>

                                {{-- 2. Origem --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Origem</span>
                                    <div>
                                        @if($order->origin === 'distributor' || ($order->user && $order->user->role === 'distributor'))
                                            <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase bg-blue-50 text-blue-600 border border-blue-100 tracking-widest">Distribuição</span>
                                        @elseif($order->origin === 'representative')
                                            <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase bg-green-50 text-green-700 border border-green-200 tracking-widest">Representante</span>
                                        @elseif($order->origin && $order->origin !== 'ecommerce')
                                            <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase bg-orange-50 text-orange-600 border border-orange-100 tracking-widest">{{ ucfirst($order->origin) }}</span>
                                        @else
                                            <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase bg-purple-50 text-purple-600 border border-purple-100 tracking-widest">E-commerce</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 3. Data --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</span>
                                    <span class="text-xs font-bold text-gray-500 flex items-center gap-1 lg:justify-center">
                                        {{ $order->created_at->format('d/m/Y') }}
                                    </span>
                                </td>

                                {{-- 4. Status --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</span>
                                    <div>
                                        <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest shadow-sm border
                                            @if($order->status == 'aguardando_confirmacao') bg-purple-50 text-purple-700 border-purple-100 @endif
                                            @if($order->status == 'pendente') bg-yellow-50 text-yellow-700 border-yellow-100 @endif
                                            @if($order->status == 'aprovado') bg-green-50 text-green-700 border-green-100 @endif
                                            @if($order->status == 'cancelado') bg-red-50 text-red-700 border-red-100 @endif
                                            @if($order->status == 'enviado') bg-blue-50 text-blue-700 border-blue-100 @endif">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </div>
                                </td>

                                {{-- 5. Total --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-right border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</span>
                                    <span class="font-black text-base text-[#c5a059]">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                                </td>

                                {{-- 6. Botão de Ação --}}
                                <td class="py-4 lg:py-5 px-2 lg:px-6 flex justify-center lg:table-cell lg:text-center border-t border-gray-50 lg:border-0 mt-2 lg:mt-0">
                                    <button
                                        @click="selectedOrder = {{ json_encode($order->load(['items.product', 'user'])) }}; openModal = true"
                                        class="w-full lg:w-auto bg-black text-[#c5a059] px-6 py-3 lg:py-2 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md border border-[#c5a059]">
                                        Gerenciar
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr class="hidden lg:table-row">
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Nenhum pedido encontrado.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>

            <div class="h-20 hide-on-print"></div>
        </div>

        {{-- MODAL DE GERENCIAMENTO E IMPRESSÃO --}}
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 print-container" style="display: none;" x-cloak>
            <div class="hide-on-print absolute inset-0 bg-black/70 backdrop-blur-sm transition-opacity" @click="openModal = false"></div>

            <div id="print-area" x-show="openModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-[2rem] sm:rounded-[3rem] shadow-2xl w-full max-w-3xl relative z-10 flex flex-col max-h-[90vh]">

                {{-- Cabeçalho do Modal --}}
                <div class="p-6 sm:p-8 border-b border-gray-100 flex justify-between items-start bg-gray-50/50 rounded-t-[2rem] sm:rounded-t-[3rem] shrink-0 print-header">
                    <div class="space-y-2 w-full">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <h3 class="font-black text-xl sm:text-2xl uppercase tracking-tighter text-black">Pedido #<span x-text="String(selectedOrder.id).padStart(5, '0')"></span></h3>
                                <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase bg-black text-[#c5a059] shadow-sm print-badge" x-text="selectedOrder.status ? selectedOrder.status.replace('_', ' ') : ''"></span>
                            </div>
                        </div>
                        <p class="text-xs font-bold text-gray-500 print-text-black mt-2">
                            Comprador:
                            <span class="text-black font-black uppercase" x-text="selectedOrder.user ? selectedOrder.user.name : 'Cliente ' + (selectedOrder.origin ? selectedOrder.origin.charAt(0).toUpperCase() + selectedOrder.origin.slice(1) : 'Marketplace')"></span>
                        </p>

                        {{-- ÁREA: FORMA DE PAGAMENTO --}}
                        <div class="mt-3 flex items-center gap-2">
                            <p class="text-[10px] font-black uppercase text-gray-400 print-text-black tracking-widest">Pagamento:</p>

                            <template x-if="selectedOrder.metodo_pagamento === 'pix_manual' || selectedOrder.metodo_pagamento === 'pix'">
                                <span class="inline-block bg-green-100 text-green-700 py-1 px-3 rounded-lg text-[9px] font-black uppercase tracking-widest border border-green-200 print-badge-plain">PIX</span>
                            </template>
                            <template x-if="selectedOrder.metodo_pagamento === 'cartao'">
                                <span class="inline-block bg-blue-100 text-blue-700 py-1 px-3 rounded-lg text-[9px] font-black uppercase tracking-widest border border-blue-200 print-badge-plain">Cartão</span>
                            </template>
                            <template x-if="!selectedOrder.metodo_pagamento || (selectedOrder.metodo_pagamento !== 'pix_manual' && selectedOrder.metodo_pagamento !== 'pix' && selectedOrder.metodo_pagamento !== 'cartao')">
                                <span class="inline-block text-gray-500 print-text-black uppercase text-[9px] font-black tracking-widest" x-text="selectedOrder.metodo_pagamento || 'Não informada'"></span>
                            </template>
                        </div>

                        {{-- ÁREA: ENDEREÇO E RASTREIO --}}
                        <div class="mt-4 p-4 sm:p-5 bg-white rounded-2xl border border-gray-100 shadow-sm print-box">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-3">
                                <p class="text-[10px] font-black uppercase text-gray-400 print-text-black tracking-widest flex items-center gap-1 border-b print-border-dark pb-1 w-full sm:w-auto">
                                    <svg class="w-3 h-3 hide-on-print" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Endereço de Entrega
                                </p>
                                <template x-if="selectedOrder.shipping_service_id">
                                    <span class="bg-[#c5a059]/10 text-[#c5a059] px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border border-[#c5a059]/20 self-start print-badge-plain"
                                          x-text="getShippingName(selectedOrder.shipping_service_id)"></span>
                                </template>
                            </div>

                            <div class="text-xs text-gray-600 print-text-black space-y-1">
                                <p x-show="selectedOrder.user?.street" class="font-medium text-sm">
                                    <span x-text="selectedOrder.user?.street"></span>, <span x-text="selectedOrder.user?.number"></span>
                                    <template x-if="selectedOrder.user?.complement">
                                        <span x-text="' - ' + selectedOrder.user?.complement"></span>
                                    </template>
                                </p>
                                <p x-show="selectedOrder.user?.neighborhood" class="text-gray-500 print-text-black">
                                    <span x-text="selectedOrder.user?.neighborhood"></span> • <span class="font-bold text-gray-700 print-text-black" x-text="selectedOrder.user?.city + ' - ' + selectedOrder.user?.state"></span>
                                </p>
                                <p x-show="selectedOrder.user?.zip_code" class="text-gray-500 print-text-black mt-1">
                                    CEP: <span class="font-bold text-gray-700 print-text-black" x-text="selectedOrder.user?.zip_code"></span>
                                </p>

                                {{-- Rastreio --}}
                                <template x-if="selectedOrder.codigo_rastreio">
                                    <div class="mt-3 pt-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between hide-on-print">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Rastreamento:</span>
                                        <a :href="'https://melhorrastreio.com.br/rastreio/' + selectedOrder.codigo_rastreio" target="_blank"
                                           class="text-[#c5a059] hover:text-black font-black text-sm uppercase tracking-wider transition-colors inline-flex items-center gap-1">
                                            <span x-text="selectedOrder.codigo_rastreio"></span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Botão Fechar (X) - Oculto na Impressão --}}
                    <button @click="openModal = false" class="hide-on-print absolute top-6 right-6 w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-400 hover:text-black hover:bg-gray-100 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Corpo do Modal Rolável (Itens) --}}
                <div class="p-6 sm:p-8 overflow-y-auto flex-1 bg-white print-content">
                    <h4 class="text-[10px] font-black text-gray-400 print-text-black border-b print-border-dark pb-1 uppercase tracking-widest mb-4">Itens do Pedido</h4>
                    <div class="space-y-3 print-space-reset">
                        <template x-for="item in selectedOrder.items" :key="item.id">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center bg-gray-50 p-4 rounded-2xl border border-gray-100 gap-3 print-item">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center border border-gray-200 font-black text-sm text-[#c5a059] shadow-sm flex-shrink-0 print-item-qty">
                                        <span x-text="item.quantidade + 'x'"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm text-gray-900 print-text-black leading-tight uppercase" x-text="item.product ? item.product.nome : 'Produto não identificado'"></span>
                                        <span class="text-[9px] text-gray-400 print-text-black uppercase font-black tracking-wider mt-0.5" x-text="'UN: R$ ' + parseFloat(item.preco_unitario).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right pl-16 sm:pl-0 print-pl-reset">
                                    <p class="text-sm font-black text-black" x-text="'R$ ' + parseFloat(item.subtotal).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Rodapé do Modal Fixo (Resumo Financeiro e Ações) --}}
                <div class="p-6 sm:p-8 bg-gray-50 border-t border-gray-100 shrink-0 rounded-b-[2rem] sm:rounded-b-[3rem] print-footer">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-end print-footer-grid">

                        {{-- Totais Detalhados --}}
                        <div class="space-y-3 lg:border-r border-gray-200 lg:pr-8 bg-white p-5 rounded-2xl shadow-sm border border-gray-100 print-totals">
                            <div class="flex justify-between text-[10px] font-black uppercase text-gray-500 print-text-black tracking-widest">
                                <span>Subtotal</span>
                                <span class="text-gray-900 print-text-black" x-text="'R$ ' + (parseFloat(selectedOrder.total || 0) - parseFloat(selectedOrder.frete || 0)).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                            </div>

                            <div class="flex justify-between items-center text-[10px] font-black uppercase text-gray-500 print-text-black tracking-widest border-b border-dashed border-gray-200 print-border-dark pb-3">
                                <span>Frete</span>
                                <span class="text-gray-900 print-text-black" x-text="'R$ ' + parseFloat(selectedOrder.frete || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                            </div>

                            <div class="flex justify-between items-center text-lg sm:text-xl font-black uppercase text-black pt-1">
                                <span>Total Final</span>
                                <span class="text-[#c5a059] print-text-black" x-text="'R$ ' + parseFloat(selectedOrder.total || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                            </div>
                        </div>

                        {{-- Ações Administrativas - OCULTAS NA IMPRESSÃO --}}
                        <div class="space-y-4 hide-on-print">
                            {{-- Formulário de Status --}}
                            <form :action="'/admin/pedidos/' + selectedOrder.id + '/status'" method="POST" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                                @csrf @method('PATCH')
                                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-2">Alterar Status</p>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="relative flex-1">
                                        <select name="status" x-model="selectedOrder.status" class="w-full bg-gray-50 border-none rounded-xl py-3 pl-4 pr-10 font-bold text-sm text-gray-700 focus:ring-2 focus:ring-[#c5a059] appearance-none cursor-pointer">
                                            <option value="pendente">Pendente</option>
                                            <option value="aprovado">Aprovado</option>
                                            <option value="em_separacao">Em Separação</option>
                                            <option value="enviado">Enviado</option>
                                            <option value="entregue">Entregue</option>
                                            <option value="cancelado">Cancelado</option>
                                            <option value="devolvido_falha">Devolvido/Falha</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full sm:w-auto bg-black text-[#c5a059] px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md">
                                        Salvar
                                    </button>
                                </div>
                            </form>

                            {{-- Botões de Ação Extras --}}
                            <div class="flex flex-col sm:flex-row gap-3">
                                {{-- Botão Imprimir Pedido --}}
                                <button type="button" @click="window.print()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest transition-all flex items-center justify-center gap-2 shadow-sm border border-gray-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Imprimir
                                </button>

                                {{-- Botão Gerar Etiqueta (Oculto para distribuidor) --}}
                                <form x-show="selectedOrder.user?.role !== 'distributor'" :action="'/admin/pedidos/' + selectedOrder.id + '/etiqueta'" method="POST" class="flex-1 flex">
                                    @csrf
                                    <button type="submit" class="w-full bg-[#c5a059] hover:bg-black text-black hover:text-[#c5a059] py-3 rounded-xl font-black uppercase text-[10px] tracking-widest transition-all flex items-center justify-center gap-2 shadow-md">
                                        <template x-if="selectedOrder.url_etiqueta">
                                            <span>Baixar Etiqueta</span>
                                        </template>
                                        <template x-if="!selectedOrder.url_etiqueta">
                                            <span>Gerar Etiqueta</span>
                                        </template>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    /* Oculta o modal até o AlpineJS carregar */
    [x-cloak] { display: none !important; }

    /* ESTILOS RÍGIDOS DE IMPRESSÃO (Sem páginas em branco) */
    @media print {
        /* Desliga tudo o que não seja estritamente necessário no DOM principal */
        nav, header, footer, aside, .hide-on-print {
            display: none !important;
        }

        /* Reseta a página para eliminar a rolagem vertical que cria as páginas em branco */
        html, body, main, .reset-on-print {
            height: auto !important;
            min-height: 0 !important;
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
        }

        /* Tira o comportamento de "flutuar" do modal e o transforma em um bloco normal */
        .print-container {
            position: relative !important;
            inset: auto !important;
            display: block !important;
            padding: 0 !important;
            z-index: 1 !important;
        }

        /* Retira sombras, bordas arredondadas e alturas máximas do recibo */
        #print-area {
            position: relative !important;
            max-height: none !important;
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            width: 100% !important;
            display: block !important;
        }

        /* Limpa o cabeçalho do recibo */
        .print-header {
            background: transparent !important;
            border-bottom: 2px solid #000 !important;
            border-radius: 0 !important;
            padding: 0 0 20px 0 !important;
        }

        /* Limpa fundos de caixas e transforma bordas e textos para preto/branco */
        .print-box { border: none !important; padding: 0 !important; box-shadow: none !important; margin-top: 20px !important; }
        .print-text-black { color: #000 !important; }
        .print-border-dark { border-color: #000 !important; }

        .print-badge {
            background: #fff !important; color: #000 !important;
            border: 1px solid #000 !important; padding: 2px 8px !important;
        }

        .print-badge-plain {
            background: transparent !important; color: #000 !important;
            border: 1px solid #000 !important; padding: 2px 8px !important;
        }

        /* Ajustes no corpo dos itens */
        .print-content { overflow: visible !important; padding: 20px 0 !important; }
        .print-space-reset { space-y: 0 !important; }

        .print-item {
            background: transparent !important;
            border-bottom: 1px solid #ddd !important;
            border-top: none !important; border-left: none !important; border-right: none !important;
            border-radius: 0 !important;
            padding: 10px 0 !important;
        }

        .print-item-qty {
            border: none !important; shadow: none !important; color: #000 !important;
            width: auto !important; height: auto !important; margin-right: 10px !important;
        }

        .print-pl-reset { padding-left: 0 !important; }

        /* Ajustes no rodapé financeiro */
        .print-footer {
            background: transparent !important; border: none !important;
            border-radius: 0 !important; padding: 20px 0 0 0 !important; margin-top: 20px !important;
        }

        .print-footer-grid { display: block !important; }

        .print-totals {
            box-shadow: none !important; border: none !important; padding: 0 !important;
            width: 250px !important; margin-left: auto !important;
        }

        /* Força precisão de cor e bordas (se o navegador suportar) */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
