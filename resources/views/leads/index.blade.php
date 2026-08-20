<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                {{ __('Leads Distribuidores') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BARRA DE BUSCA PREMIUM --}}
            <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-gray-100 shadow-sm mb-6">
                <form action="{{ route('leads.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar por Nome, Cidade ou E-mail..."
                               class="w-full bg-gray-50 border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-[#c5a059] font-bold text-gray-700 placeholder-gray-400 shadow-inner">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-black text-[#c5a059] px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-[#c5a059] hover:text-black transition-all shadow-md">
                            Buscar
                        </button>
                        @if($search)
                            <a href="{{ route('leads.index') }}" class="bg-gray-100 text-gray-400 px-6 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-red-50 hover:text-red-500 transition-all flex items-center justify-center">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- CONTADOR DE RESULTADOS --}}
            <div class="mb-6 flex items-center">
                <span class="bg-[#c5a059] text-black text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-full shadow-sm">
                    {{ $leads->count() }} {{ Str::plural('Lead', $leads->count()) }} {{ $search ? 'encontrado(s)' : 'no total' }}
                </span>
            </div>

            {{-- LISTA DE LEADS EM CARTÕES (Substituindo a Tabela) --}}
            <div class="space-y-4">
                @forelse($leads as $lead)
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between p-6 bg-white rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 gap-6 group">

                        {{-- Avatar e Informações --}}
                        <div class="flex items-center gap-5 w-full lg:w-auto">
                            <div class="w-14 h-14 rounded-full bg-black text-[#c5a059] flex items-center justify-center font-black text-xl uppercase shadow-inner flex-shrink-0">
                                {{ mb_substr($lead->nome, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-lg leading-tight">{{ $lead->nome }}</h4>
                                <div class="flex flex-col sm:flex-row sm:items-center text-xs text-gray-500 mt-1 gap-1 sm:gap-2">
                                    <span class="font-bold text-gray-700">{{ $lead->cidade }} / {{ $lead->estado }}</span>
                                    <span class="hidden sm:inline text-gray-300">•</span>
                                    <span class="text-[10px] uppercase tracking-wider font-bold text-gray-400 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $lead->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Ações (WhatsApp, Promover, Excluir) --}}
                        <div class="flex items-center gap-3 w-full lg:w-auto pt-4 lg:pt-0 border-t lg:border-0 border-gray-50">

                            {{-- Botão WhatsApp --}}
                            <a href="https://wa.me/55{{ preg_replace('/\D/', '', $lead->whatsapp) }}" target="_blank"
                               class="w-12 h-12 flex items-center justify-center bg-green-50 text-green-600 rounded-2xl hover:bg-green-500 hover:text-white transition-all duration-300 shadow-sm"
                               title="Chamar no WhatsApp">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.883-.653-1.48-1.459-1.653-1.756-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                            </a>

                            {{-- Botão Promover --}}
                            <form action="{{ route('leads.promote', $lead->id) }}" method="POST" onsubmit="return confirm('Confirmar este lead como Distribuidor Oficial?')" class="flex-1 lg:flex-none">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-black text-[#c5a059] px-6 py-3 h-12 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-[#c5a059] hover:text-black transition-all border border-[#c5a059] shadow-md">
                                    Promover
                                </button>
                            </form>

                            {{-- Botão Excluir --}}
                            <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Deseja realmente remover este lead?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-12 h-12 flex items-center justify-center bg-red-50 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Excluir Lead">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-[2rem] border-2 border-dashed border-gray-200 p-16 text-center flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Nenhum lead encontrado para esta busca.</p>
                    </div>
                @endforelse
            </div>

            <div class="h-20"></div>
        </div>
    </div>
</x-app-layout>
