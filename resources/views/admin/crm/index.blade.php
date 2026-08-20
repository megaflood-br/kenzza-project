<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
            Carteira de <span class="text-[#c5a059]">Clientes</span>
        </h2>
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Gestão de Distribuidores Autorizados</p>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-[2rem] border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="hidden lg:table-header-group bg-gray-50">
                        <tr class="border-b border-gray-100">
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">Distribuidor</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Contato / Local</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Pedidos</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-right">LTV (Total Comprado)</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody class="flex flex-col lg:table-row-group divide-y lg:divide-y-0 divide-gray-100 p-4 lg:p-0">
                        @forelse($distributors as $distributor)
                            <tr class="flex flex-col lg:table-row bg-white lg:hover:bg-gray-50 transition-all p-4 lg:p-0 rounded-2xl lg:rounded-none border lg:border-0 border-gray-100 lg:border-b mb-4 lg:mb-0 shadow-sm lg:shadow-none">

                                {{-- 1. Distribuidor (Nome e E-mail) --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center border-t border-gray-50 lg:border-0">
                                    <div class="text-right lg:text-left">
                                        <div class="text-sm font-bold text-gray-900">{{ $distributor->name }}</div>
                                        {{-- Agora usamos $distributor->role corretamente --}}
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] uppercase font-black tracking-widest
                                            {{ $distributor->role === 'representative' ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-800' }}">
                                            {{ $distributor->role === 'representative' ? 'Representante' : 'Distribuidor' }}
                                        </span>
                                         <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mt-0.5">{{ $distributor->email }}</p>

                                    </div>
                                </td>
                                {{-- 2. Contato / Local --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Contato</span>
                                    <div class="text-right lg:text-center">
                                        <p class="font-bold text-xs text-gray-700">{{ $distributor->telefone ?? $distributor->phone ?? 'Não informado' }}</p>
                                        <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mt-0.5">
                                            {{ $distributor->city ?? 'Cidade' }} - {{ $distributor->state ?? 'UF' }}
                                        </p>
                                    </div>
                                </td>

                                {{-- 3. Pedidos Emitidos --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Pedidos</span>
                                    <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase bg-gray-100 text-gray-700 tracking-widest">
                                        {{ $distributor->orders_count }} {{ $distributor->orders_count === 1 ? 'Pedido' : 'Pedidos' }}
                                    </span>
                                </td>

                                {{-- 4. Total Gasto (LTV) --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-right border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Comprado</span>
                                    <span class="font-black text-base text-[#c5a059]">
                                        R$ {{ number_format($distributor->orders_sum_total ?? 0, 2, ',', '.') }}
                                    </span>
                                </td>

                                {{-- 5. Botões de Ação --}}
                                <td class="py-4 lg:py-5 px-2 lg:px-6 flex justify-center lg:table-cell lg:text-center border-t border-gray-50 lg:border-0 mt-2 lg:mt-0">
                                    <div class="flex gap-2 w-full lg:w-auto justify-center">

                                        {{-- Botão WhatsApp (Limpa o número para passar na URL corretamente) --}}
                                        @php
                                            $numeroLimpo = preg_replace('/[^0-9]/', '', $distributor->telefone ?? $distributor->phone ?? '');
                                        @endphp

                                        @if($numeroLimpo)
                                            <a href="https://wa.me/55{{ $numeroLimpo }}" target="_blank" class="flex-1 lg:flex-none bg-green-500 text-white px-4 py-2 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-green-600 transition-all shadow-md flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.12.552 4.17 1.6 6.015L.135 24l6.115-1.59c1.78.96 3.785 1.47 5.78 1.47 6.645 0 12.03-5.385 12.03-12.03S18.675 0 12.031 0zm0 21.84c-1.785 0-3.54-.48-5.085-1.395l-.36-.21-3.78.99.99-3.69-.24-.375c-1.02-1.59-1.56-3.42-1.56-5.28 0-5.505 4.485-9.99 9.99-9.99s9.99 4.485 9.99 9.99-4.485 9.99-9.99 9.99zm5.49-7.5c-.3-.15-1.785-.885-2.055-.99-.285-.105-.495-.15-.705.15-.21.3-.78.99-.96 1.2-.18.195-.36.225-.66.075-1.725-.825-2.88-1.575-3.99-3.33-.195-.315.225-.285.81-1.455.09-.15.045-.285-.03-.435-.075-.15-.705-1.71-.975-2.34-.255-.615-.525-.525-.705-.54h-.6c-.21 0-.555.075-.84.39-.285.315-1.095 1.065-1.095 2.595s1.125 3.015 1.29 3.225c.15.21 2.205 3.36 5.34 4.635 2.07.84 2.895.915 3.975.765.885-.12 2.31-.945 2.64-1.86.33-.915.33-1.695.225-1.86-.09-.15-.315-.24-.615-.39z"/></svg>
                                                WhatsApp
                                            </a>
                                        @endif

                                        {{-- Botão Direto para Emitir Pedido --}}
                                        <a href="{{ route('crm.orders.create', ['distributor_id' => $distributor->id]) }}" class="flex-1 lg:flex-none bg-black text-[#c5a059] px-4 py-2 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md border border-[#c5a059] flex items-center justify-center">
                                            Novo Pedido
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr class="hidden lg:table-row">
                                <td colspan="5" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Nenhum distribuidor cadastrado.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($distributors->hasPages())
                    <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                        {{ $distributors->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
