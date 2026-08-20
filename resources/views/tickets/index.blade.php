<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-black text-2xl text-gray-900 uppercase tracking-tighter">
                Centro de <span class="text-[#c5a059]">Suporte</span>
            </h2>
            <a href="{{ route('tickets.create') }}" class="w-full sm:w-auto text-center bg-black text-[#c5a059] px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md border border-[#c5a059]">
                + Abrir Chamado
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-bold rounded-2xl uppercase text-xs tracking-widest shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- LISTA DE CHAMADOS (HÍBRIDA: Tabela no PC / Cards no Celular) --}}
            <div class="bg-white shadow-sm sm:rounded-[2rem] border border-gray-100 overflow-hidden">

                {{-- Oculta o cabeçalho no celular --}}
                <table class="w-full text-left border-collapse">
                    <thead class="hidden lg:table-header-group bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">Chamado</th>
                            @if(auth()->user()->role !== 'distributor')
                                <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Distribuidor</th>
                            @endif
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Prioridade</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Status</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Data</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-right">Ação</th>
                        </tr>
                    </thead>

                    <tbody class="flex flex-col lg:table-row-group divide-y lg:divide-y-0 divide-gray-100 p-4 lg:p-0 space-y-4 lg:space-y-0">
                        @forelse ($tickets as $ticket)
                            <tr class="flex flex-col lg:table-row bg-white lg:hover:bg-gray-50/50 transition-all p-5 lg:p-0 rounded-3xl lg:rounded-none border lg:border-0 border-gray-100 lg:border-b shadow-sm lg:shadow-none group cursor-pointer" onclick="window.location='{{ route('tickets.show', $ticket->id) }}'">

                                {{-- 1. Chamado (Assunto e Protocolo) --}}
                                <td class="py-2 lg:py-5 px-2 lg:px-6 flex justify-start lg:table-cell items-center">
                                    <div class="flex items-center gap-4 w-full">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex-shrink-0 flex items-center justify-center border border-gray-200 text-gray-400 group-hover:text-[#c5a059] group-hover:border-[#c5a059]/30 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-bold text-sm lg:text-base text-gray-900 leading-tight">{{ $ticket->subject }}</p>
                                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- 2. Distribuidor (Apenas para Admins/Editores) --}}
                                @if(auth()->user()->role !== 'distributor')
                                    <td class="py-2 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0 mt-4 lg:mt-0 pt-4 lg:pt-0">
                                        <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Distribuidor</span>
                                        <span class="text-xs font-bold text-gray-600">{{ $ticket->user->name }}</span>
                                    </td>
                                @endif

                                {{-- 3. Prioridade --}}
                                <td class="py-2 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0 pt-3 lg:pt-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Prioridade</span>
                                    <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest border
                                        {{ $ticket->priority == 'high' ? 'bg-red-50 text-red-600 border-red-100' : ($ticket->priority == 'medium' ? 'bg-orange-50 text-orange-600 border-orange-100' : 'bg-gray-50 text-gray-500 border-gray-200') }}">
                                        {{ $ticket->priority_label }}
                                    </span>
                                </td>

                                {{-- 4. Status --}}
                                <td class="py-2 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0 pt-3 lg:pt-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</span>
                                    <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border shadow-sm
                                        {{ $ticket->status == 'open' ? 'bg-green-50 text-green-600 border-green-100' : ($ticket->status == 'in_progress' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-gray-50 text-gray-500 border-gray-200') }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                </td>

                                {{-- 5. Data --}}
                                <td class="py-2 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0 pt-3 lg:pt-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</span>
                                    <span class="text-xs font-bold text-gray-500 flex items-center gap-1 lg:justify-center">
                                        <svg class="w-3 h-3 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>

                                {{-- 6. Ação (Seta indicando clique) --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-end lg:table-cell items-center lg:text-right border-t border-gray-50 lg:border-0 pt-4 lg:pt-0 mt-2 lg:mt-0">
                                    <div class="flex justify-end items-center gap-2 text-gray-300 group-hover:text-[#c5a059] transition-colors font-black text-[10px] uppercase tracking-widest">
                                        <span class="lg:hidden text-gray-400">Ver Detalhes</span>
                                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="hidden lg:table-row">
                                <td colspan="{{ auth()->user()->role !== 'distributor' ? '6' : '5' }}" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        </div>
                                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Nenhum chamado encontrado.</p>
                                    </div>
                                </td>
                            </tr>
                            {{-- Empty State (Mobile) --}}
                            <div class="lg:hidden bg-white rounded-[2rem] border-2 border-dashed border-gray-100 p-12 text-center flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Nenhum chamado encontrado.</p>
                            </div>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="h-20"></div>
        </div>
    </div>
</x-app-layout>
