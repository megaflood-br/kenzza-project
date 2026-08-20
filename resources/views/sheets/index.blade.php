<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tighter uppercase">
                    {{ __('Fichas Técnicas') }}
                </h2>
                <p class="text-[10px] text-[#c5a059] font-bold uppercase tracking-[0.3em]">Patrimônio K'enzza Professional</p>
            </div>

            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
                <a href="{{ route('sheets.create') }}" class="bg-black text-[#c5a059] px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-[#c5a059] hover:text-black transition-all duration-300 shadow-xl border border-[#c5a059]/30 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                    Nova Ficha
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($sheets as $sheet)
                    <div class="group bg-white rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 flex flex-col relative">

                        <div class="absolute top-5 left-5 z-10">
                            <span class="bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest text-gray-900 shadow-sm border border-gray-100">
                                Professional Line
                            </span>
                        </div>

                        <div class="relative h-72 overflow-hidden bg-white flex items-center justify-center p-8">
                            @if($sheet->foto)
                                <img src="{{ asset('storage/' . $sheet->foto) }}"
                                     class="max-h-full object-contain transform group-hover:scale-110 transition-transform duration-700 ease-in-out"
                                     alt="{{ $sheet->nome }}">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>

                        <div class="p-8 flex flex-col flex-1">
                            <div class="mb-4">
                                <h3 class="font-black text-xl text-gray-900 mb-2 leading-tight group-hover:text-[#c5a059] transition-colors">
                                    {{ $sheet->nome }}
                                </h3>
                                <div class="w-10 h-1 bg-[#c5a059] rounded-full mb-4"></div>
                                <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed italic">
                                    {{ $sheet->descricao }}
                                </p>
                            </div>

                            <div class="pt-6 mt-auto border-t border-gray-50 flex flex-col gap-4">

                                <div class="flex items-center justify-between">
                                    <a href="{{ Auth::user()->role === 'distributor' ? route('sheets.show.distributor', $sheet->id) : route('sheets.show', $sheet->id) }}"
   class="flex-1 bg-black text-white text-center py-3 rounded-xl text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-[#c5a059] hover:text-black transition-all duration-300">
    Explorar Ficha
</a>

                                    @if($sheet->pdf)
                                        <a href="{{ asset('storage/' . $sheet->pdf) }}" target="_blank" class="ml-3 p-3 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Baixar PDF">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        </a>
                                    @endif
                                </div>

                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
                                    <div class="flex items-center justify-center gap-6 pt-2">
                                        <a href="{{ route('sheets.edit', $sheet->id) }}" class="text-[9px] font-bold text-gray-400 hover:text-black uppercase tracking-widest flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            Editar
                                        </a>

                                        <form action="{{ route('sheets.destroy', $sheet->id) }}" method="POST" onsubmit="return confirm('Deseja excluir esta ficha permanentemente?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-[9px] font-bold text-red-300 hover:text-red-600 uppercase tracking-widest flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-[2.5rem] p-20 text-center border-2 border-dashed border-gray-100">
                        <p class="text-gray-400 font-bold uppercase tracking-widest">Nenhuma ficha técnica encontrada no acervo.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
