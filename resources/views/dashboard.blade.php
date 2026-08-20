<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
            Resumo <span class="text-[#c5a059]">K'enzza Comercial</span>
        </h2>
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Dados de performance da carteira B2B</p>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ========================================== --}}
            {{-- VISÃO DA DIRETORIA / GERÊNCIA (ADMIN, MANAGER, EDITOR) --}}
            {{-- ========================================== --}}
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'editor']))

                {{-- CARDS SUPERIORES --}}
                <div class="grid grid-cols-1 md:grid-cols-2 {{ auth()->user()->role === 'admin' ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-6 mb-8">
                    {{-- Card 1: Faturamento B2B --}}
                    <div class="bg-black text-white p-6 rounded-[2rem] border border-gray-900 shadow-sm flex items-center gap-5 relative overflow-hidden group">
                        <div class="w-12 h-12 bg-[#c5a059]/10 rounded-2xl flex items-center justify-center text-[#c5a059] flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest leading-none mb-1">Faturamento B2B (Mês)</p>
                            <h3 class="text-xl font-black text-[#c5a059]">R$ {{ number_format($faturamentoB2B, 2, ',', '.') }}</h3>
                        </div>
                    </div>

                    {{-- CARD EXCLUSIVO ADMIN --}}
                    @if(auth()->user()->role === 'admin')
                        <div class="bg-gradient-to-br from-amber-950 to-black text-white p-6 rounded-[2rem] border border-amber-900/30 shadow-sm flex items-center gap-5 relative overflow-hidden group">
                            <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-400 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase text-amber-400/70 tracking-widest leading-none mb-1">E-commerce B2C (Mês)</p>
                                <h3 class="text-xl font-black text-amber-400">R$ {{ number_format($faturamentoEcommerce, 2, ',', '.') }}</h3>
                            </div>
                        </div>
                    @endif

                    {{-- Card 2: Pedidos --}}
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-700 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest leading-none mb-1">Pedidos no Mês</p>
                            <h3 class="text-2xl font-black text-gray-900">{{ $pedidosMesCount }}</h3>
                        </div>
                    </div>

                    {{-- Card 3: Aguardando --}}
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest leading-none mb-1">Aguardando Envio</p>
                            <h3 class="text-2xl font-black {{ $aguardandoLiberacao > 0 ? 'text-orange-600' : 'text-gray-900' }}">{{ $aguardandoLiberacao }}</h3>
                        </div>
                    </div>

                    {{-- Card 4: Distribuidores --}}
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-700 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest leading-none mb-1">Total de Distribuidores</p>
                            <h3 class="text-2xl font-black text-gray-900">{{ $totalDistribuidores }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Card 5: Comissão (Apenas Admin e Manager) --}}
                @if(in_array(auth()->user()->role, ['admin', 'manager']))
                    <div class="bg-gradient-to-br from-green-900 to-black text-white p-6 rounded-[2rem] border border-green-800 shadow-sm flex items-center gap-5 mb-8">
                        <div class="w-12 h-12 bg-green-500/20 rounded-2xl flex items-center justify-center text-green-400 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase text-green-400/70 tracking-widest leading-none mb-1">Comissão do gerente no Mês</p>
                            <h3 class="text-xl font-black text-green-400">R$ {{ number_format($comissoesMes, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                @endif
            @endif


            {{-- ========================================== --}}
            {{-- VISÃO DO REPRESENTANTE (AÇÕES RÁPIDAS) --}}
            {{-- ========================================== --}}
            @if(auth()->user()->role === 'representative')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    {{-- Botão: Novo Pedido --}}
                    <a href="{{ route('orders.create') }}" class="bg-[#c5a059] text-white p-8 rounded-[2rem] text-center shadow-sm flex flex-col items-center justify-center hover:scale-105 transition-transform">
                        <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span class="font-black uppercase tracking-widest text-sm">Novo Pedido</span>
                    </a>

                    {{-- Botão: Fichas Técnicas --}}
                    <a href="{{ route('sheets.index') }}" class="bg-black text-white p-8 rounded-[2rem] text-center shadow-sm flex flex-col items-center justify-center hover:scale-105 transition-transform">
                        <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="font-black uppercase tracking-widest text-sm">Fichas Técnicas</span>
                    </a>

                    {{-- Botão: Kits de Mídia --}}
                    <a href="{{ route('media.index') }}" class="bg-gray-800 text-white p-8 rounded-[2rem] text-center shadow-sm flex flex-col items-center justify-center hover:scale-105 transition-transform">
                        <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="font-black uppercase tracking-widest text-sm">Kits de Mídia</span>
                    </a>
                </div>
            @endif


            {{-- ========================================== --}}
            {{-- TABELA DE PEDIDOS (Comum a todos) --}}
            {{-- ========================================== --}}
            <div class="bg-white shadow-sm rounded-[2rem] border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-tighter text-gray-900">
                            {{ auth()->user()->role === 'representative' ? 'Meus Últimos Pedidos' : 'Últimas Movimentações B2B' }}
                        </h3>
                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mt-0.5">
                            {{ auth()->user()->role === 'representative' ? 'Histórico recente de suas compras' : 'Pedidos recentes da carteira de distribuição' }}
                        </p>
                    </div>

                    {{-- Botão "Ver Todos" dinâmico conforme o nível de acesso --}}
                    @if(auth()->user()->role === 'representative')
                        <a href="{{ route('orders.index') }}" class="bg-gray-100 text-gray-700 hover:bg-black hover:text-[#c5a059] px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            Ver Todos
                        </a>
                    @else
                        <a href="{{ route('admin.orders.index', ['origin' => 'distributor']) }}" class="bg-gray-100 text-gray-700 hover:bg-black hover:text-[#c5a059] px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            Ver Todos
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                <th class="py-4 px-6 text-[9px] font-black uppercase text-gray-400 tracking-widest">ID Pedido</th>
                                <th class="py-4 px-6 text-[9px] font-black uppercase text-gray-400 tracking-widest">Distribuidor / Cliente</th>
                                <th class="py-4 px-6 text-[9px] font-black uppercase text-gray-400 tracking-widest text-center">Data</th>
                                <th class="py-4 px-6 text-[9px] font-black uppercase text-gray-400 tracking-widest text-center">Pagamento</th>
                                <th class="py-4 px-6 text-[9px] font-black uppercase text-gray-400 tracking-widest text-center">Status</th>
                                <th class="py-4 px-6 text-[9px] font-black uppercase text-gray-400 tracking-widest text-right">Total</th>

                                {{-- Apenas Admin, Manager e Editor veem a coluna de comissão --}}
                                @if(in_array(auth()->user()->role, ['admin', 'manager', 'editor']))
                                    <th class="py-4 px-6 text-[9px] font-black uppercase text-gray-400 tracking-widest text-right">Comissão</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($ultimosPedidosB2B as $pedido)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-6 font-black text-sm text-gray-900">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-sm text-gray-800 leading-tight">{{ $pedido->user?->name ?? 'Não Identificado' }}</p>
                                        <p class="text-[9px] text-gray-400 uppercase font-black tracking-wider mt-0.5">{{ $pedido->user?->city ?? 'Cidade não informada' }}</p>
                                    </td>
                                    <td class="py-4 px-6 text-center text-xs font-bold text-gray-500">{{ $pedido->created_at->format('d/m/Y') }}</td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-block bg-gray-100 text-gray-700 py-1 px-3 rounded-lg text-[9px] font-black uppercase tracking-widest border border-gray-200">
                                            {{ $pedido->metodo_pagamento ?? 'A Combinar' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border
                                            @if($pedido->status == 'pendente') bg-yellow-50 text-yellow-700 border-yellow-100 @endif
                                            @if($pedido->status == 'aprovado') bg-green-50 text-green-700 border-green-100 @endif
                                            @if($pedido->status == 'em_separacao') bg-purple-50 text-purple-700 border-purple-100 @endif
                                            @if($pedido->status == 'enviado') bg-blue-50 text-blue-700 border-blue-100 @endif
                                            @if($pedido->status == 'entregue') bg-gray-100 text-gray-700 border-gray-200 @endif">
                                            {{ str_replace('_', ' ', $pedido->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right font-black text-sm text-[#c5a059]">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>

                                    {{-- Apenas Admin, Manager e Editor veem o valor da comissão --}}
                                    @if(in_array(auth()->user()->role, ['admin', 'manager', 'editor']))
                                        <td class="py-4 px-6 text-center">
                                            @if($pedido->comissao_gerente > 0)
                                                <span class="text-[10px] font-black text-green-600 bg-green-50 px-2 py-1 rounded-lg">+R$ {{ number_format($pedido->comissao_gerente, 2, ',', '.') }}</span>
                                            @else
                                                <span class="text-[10px] font-black text-gray-300">-</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    {{-- O colspan dinâmico evita que a tabela quebre o layout --}}
                                    <td colspan="{{ in_array(auth()->user()->role, ['admin', 'manager', 'editor']) ? 7 : 6 }}" class="py-12 text-center">
                                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Nenhum pedido emitido recentemente.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
