<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catálogo de Fichas Técnicas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 text-gray-600 text-sm">
                Olá, **{{ auth()->user()->name }}**. Abaixo você encontra todo o suporte técnico dos produtos K'enzza para download.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($sheets as $sheet)
                    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 flex flex-col hover:shadow-lg transition-shadow duration-300">
                        <div class="h-56 bg-gray-50 p-6 flex items-center justify-center border-b border-gray-50">
                            @if($sheet->foto)
                                <img src="{{ asset('storage/' . $sheet->foto) }}" class="max-h-full object-contain">
                            @else
                                <span class="text-gray-300 italic text-xs">Foto não disponível</span>
                            @endif
                        </div>

                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="font-bold text-lg text-gray-900 mb-2 uppercase tracking-tight">{{ $sheet->nome }}</h3>
                            <p class="text-sm text-gray-500 line-clamp-3 mb-6 flex-1">{{ $sheet->descricao }}</p>

                            <div class="pt-4 border-t border-gray-100 flex flex-col gap-2">
                                @if($sheet->pdf)
                                    <a href="{{ asset('storage/' . $sheet->pdf) }}" target="_blank" class="w-full bg-black text-[#c5a059] text-center py-3 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all">
                                        Download Ficha PDF
                                    </a>
                                @endif

                                <button onclick="alert('Visualização rápida em breve!')" class="w-full border border-gray-200 text-gray-600 text-center py-2 rounded-xl font-bold uppercase text-[9px] tracking-widest hover:bg-gray-50 transition-all">
                                    Ver Detalhes
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-dashed border-gray-300 text-gray-400">
                        Nenhuma ficha técnica disponível no momento.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
